<?php
// Lab 03: Cross-Site Scripting (XSS)
// Reflected XSS & Stored XSS Guestbook

// Set simulated admin cookie for testing cookie exfiltration
if (!isset($_COOKIE['SESSION_ID'])) {
    setcookie('SESSION_ID', 'ADMIN_TOKEN_FLAG{XSS_COOKIE_STEALER_PWNED}', time() + 3600, '/');
}

// Stored XSS Database file
$guestbook_file = __DIR__ . '/comments.json';
if (!file_exists($guestbook_file)) {
    file_put_contents($guestbook_file, json_encode([
        ['author' => 'SecurityBot', 'comment' => 'Welcome to the public community board!']
    ]));
}

// Handle Stored Comment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $author = $_POST['author'] ?? 'Anonymous';
    $comment = $_POST['comment'] ?? '';
    
    $comments = json_decode(file_get_contents($guestbook_file), true);
    // VULNERABILITY: Storing raw unescaped input
    $comments[] = ['author' => $author, 'comment' => $comment];
    file_put_contents($guestbook_file, json_encode($comments));
}

// Handle Clear Comments
if (isset($_GET['clear'])) {
    file_put_contents($guestbook_file, json_encode([
        ['author' => 'SecurityBot', 'comment' => 'Board reset.']
    ]));
    header("Location: index.php");
    exit;
}

$comments = json_decode(file_get_contents($guestbook_file), true);
$search = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab 03: Cross-Site Scripting (XSS)</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #0d1117; color: #c9d1d9; padding: 2rem; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2 { color: #58a6ff; }
        .box { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; }
        input[type="text"], textarea { background: #0d1117; border: 1px solid #30363d; color: #fff; padding: 0.6rem 0.8rem; border-radius: 6px; width: 100%; box-sizing: border-box; margin-top: 0.4rem; margin-bottom: 1rem; }
        button { background: #238636; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #2ea043; }
        .comment-card { background: #21262d; border-left: 4px solid #58a6ff; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; border-radius: 4px; }
        .comment-author { font-weight: bold; color: #79c0ff; margin-bottom: 0.3rem; }
        code { background: #21262d; color: #ff7b72; padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; }
        a { color: #58a6ff; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="../">← Back to Practice Labs Dashboard</a> | <a href="LAB_WALKTHROUGH.md" target="_blank">📖 Open Lab Walkthrough</a></p>
        <h1>💥 Lab 03: Cross-Site Scripting (XSS)</h1>
        <p>Your objective is to execute JavaScript in the victim's browser context and access the secret cookie token.</p>

        <!-- Challenge 1: Reflected XSS -->
        <div class="box">
            <h2>🎯 Challenge 1: Reflected XSS (Search Bar)</h2>
            <form method="GET">
                <input type="text" name="q" value="" placeholder="Search articles...">
                <button type="submit">Search</button>
            </form>

            <?php if ($search !== ''): ?>
                <div style="margin-top:1rem; padding:0.8rem; background:#21262d; border-radius:6px;">
                    <!-- VULNERABILITY: Raw reflection of $search without htmlspecialchars -->
                    Search results for: <strong><?= $search ?></strong> (0 articles found)
                </div>
            <?php endif; ?>
        </div>

        <!-- Challenge 2: Stored XSS -->
        <div class="box">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2>🎯 Challenge 2: Stored XSS (Guestbook)</h2>
                <a href="?clear=1" style="font-size:0.85rem; color:#f85149;">[Reset Guestbook]</a>
            </div>
            
            <form method="POST">
                <label>Your Name:</label>
                <input type="text" name="author" placeholder="Hacker">
                <label>Your Message:</label>
                <textarea name="comment" rows="3" placeholder="Leave a friendly message..."></textarea>
                <button type="submit">Post Comment</button>
            </form>

            <h3 style="margin-top:2rem;">Community Messages</h3>
            <?php foreach ($comments as $c): ?>
                <div class="comment-card">
                    <!-- VULNERABILITY: Both author and comment rendered unescaped -->
                    <div class="comment-author"><?= $c['author'] ?></div>
                    <div><?= $c['comment'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
