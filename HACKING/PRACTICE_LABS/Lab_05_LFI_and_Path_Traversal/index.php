<?php
// Lab 05: Local File Inclusion (LFI) & Path Traversal
$default_page = 'about.php';
$page = $_GET['page'] ?? $default_page;

$content = '';
$error = '';

if ($page) {
    // VULNERABILITY: Direct include of user-controlled path
    // Developer attempted a weak blacklist blocking only "../" once
    $sanitized_page = str_replace('../', '', $page);
    
    // Check if file exists or can be read via stream wrapper
    ob_start();
    $included = @include($page);
    $content = ob_get_clean();
    
    if (!$included && empty($content)) {
        $error = "Failed to load page template: " . htmlspecialchars($page);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab 05: Local File Inclusion (LFI)</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #0d1117; color: #c9d1d9; padding: 2rem; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2 { color: #e3b341; }
        .box { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; }
        nav { margin-bottom: 1.5rem; border-bottom: 1px solid #30363d; padding-bottom: 0.8rem; }
        nav a { color: #58a6ff; text-decoration: none; margin-right: 1.5rem; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        .content-box { background: #0d1117; border: 1px solid #30363d; border-radius: 6px; padding: 1.5rem; min-height: 150px; }
        .alert { background: rgba(218, 54, 51, 0.2); border: 1px solid #da3633; color: #f85149; padding: 0.8rem; border-radius: 6px; }
        code { background: #21262d; color: #ff7b72; padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; }
        a { color: #58a6ff; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="../">← Back to Practice Labs Dashboard</a> | <a href="LAB_WALKTHROUGH.md" target="_blank">📖 Open Lab Walkthrough</a></p>
        <h1>📂 Lab 05: Local File Inclusion & PHP Wrappers</h1>
        <p>This web application uses dynamic template inclusion to render different subpages.</p>

        <div class="box">
            <nav>
                <a href="?page=about.php">About Us</a>
                <a href="?page=contact.php">Contact</a>
                <a href="?page=secret_vault.php">Secret Vault</a>
            </nav>

            <p style="font-size:0.85rem; color:#8b949e;">Current URL parameter: <code>page=<?= htmlspecialchars($page) ?></code></p>

            <div class="content-box">
                <?php if ($error): ?>
                    <div class="alert"><?= $error ?></div>
                <?php else: ?>
                    <?= $content ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
