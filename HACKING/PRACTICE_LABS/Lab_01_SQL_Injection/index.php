<?php
// Lab 01: SQL Injection (SQLi)
// Uses SQLite so it runs anywhere without external database setup.

$db_file = __DIR__ . '/lab1.sqlite';
$init_db = !file_exists($db_file);
$db = new PDO('sqlite:' . $db_file);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

// Auto-seed database if new
if ($init_db) {
    $db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, role TEXT, secret_token TEXT)");
    $db->exec("INSERT INTO users VALUES (1, 'admin', 'SuperSecretAdminP@ss2026!', 'administrator', 'FLAG{SQLI_AUTH_BYPASS_PWNED}')");
    $db->exec("INSERT INTO users VALUES (2, 'john', 'password123', 'user', 'FLAG{USER_JOHN_TOKEN_456}')");
    $db->exec("INSERT INTO users VALUES (3, 'alice', 'Wonderland99!', 'user', 'FLAG{USER_ALICE_TOKEN_789}')");

    $db->exec("CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, category TEXT, price REAL, description TEXT)");
    $db->exec("INSERT INTO products VALUES (1, 'Tactical Keyboard', 'Hardware', 89.99, 'Mechanical stealth switches')");
    $db->exec("INSERT INTO products VALUES (2, 'Wi-Fi Pineapple Mark VII', 'Hardware', 199.99, 'Rogue AP testing platform')");
    $db->exec("INSERT INTO products VALUES (3, 'Burp Suite Cheat Sheet', 'Books', 19.99, 'Essential payloads and shortcuts')");
    $db->exec("INSERT INTO products VALUES (4, 'Rubber Ducky USB', 'Hardware', 59.99, 'Keystroke injection tool')");
    $db->exec("INSERT INTO products VALUES (5, 'Cybersecurity Hoodie', 'Apparel', 49.99, 'Black embroidered offensive security hoodie')");
}

$login_msg = '';
$login_success = false;

// Challenge 1: Authentication Bypass (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    // VULNERABILITY: Raw string concatenation in SQL query
    $query = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $stmt = $db->query($query);
    
    if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $login_success = true;
        $login_msg = "<div class='alert success'>✅ <strong>Access Granted!</strong> Welcome, " . htmlspecialchars($row['username']) . "!<br>Role: " . htmlspecialchars($row['role']) . "<br>Secret Flag: <code>" . htmlspecialchars($row['secret_token']) . "</code></div>";
    } else {
        $login_msg = "<div class='alert danger'>❌ Invalid username or password! (Query: <code>" . htmlspecialchars($query) . "</code>)</div>";
    }
}

// Challenge 2: UNION-based Product Search (GET)
$search = $_GET['search'] ?? '';
$products = [];
$search_query_debug = '';

if ($search !== '') {
    // VULNERABILITY: Raw concatenation into SELECT query
    $search_query_debug = "SELECT id, name, category, price, description FROM products WHERE name LIKE '%$search%'";
    $stmt = $db->query($search_query_debug);
    if ($stmt) {
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $errorInfo = $db->errorInfo();
        $search_query_debug .= " [SQL Error: " . htmlspecialchars($errorInfo[2] ?? '') . "]";
    }
} else {
    $stmt = $db->query("SELECT id, name, category, price, description FROM products");
    if ($stmt) {
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab 01: SQL Injection (SQLi)</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #0d1117; color: #c9d1d9; padding: 2rem; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2 { color: #58a6ff; }
        .box { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; }
        input[type="text"], input[type="password"] { background: #0d1117; border: 1px solid #30363d; color: #fff; padding: 0.5rem 0.8rem; border-radius: 6px; width: 100%; box-sizing: border-box; margin-top: 0.4rem; margin-bottom: 1rem; }
        button { background: #238636; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #2ea043; }
        .alert { padding: 1rem; border-radius: 6px; margin-top: 1rem; }
        .alert.success { background: rgba(46, 160, 67, 0.2); border: 1px solid #2ea043; color: #3fb950; }
        .alert.danger { background: rgba(218, 54, 51, 0.2); border: 1px solid #da3633; color: #f85149; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #30363d; padding: 0.6rem 0.8rem; text-align: left; }
        th { background: #21262d; color: #58a6ff; }
        code { background: #21262d; color: #ff7b72; padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; }
        a { color: #58a6ff; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="../">← Back to Practice Labs Dashboard</a> | <a href="LAB_WALKTHROUGH.md" target="_blank">📖 Open Lab Walkthrough</a></p>
        <h1>🧪 Lab 01: SQL Injection (SQLi)</h1>
        <p>This lab demonstrates two classic forms of SQL injection: <strong>Authentication Bypass</strong> and <strong>UNION-Based Data Exfiltration</strong>.</p>

        <!-- Challenge 1 -->
        <div class="box">
            <h2>🎯 Challenge 1: Administrator Authentication Bypass</h2>
            <p>Can you log in as <code>admin</code> without knowing the password?</p>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <label>Username:</label>
                <input type="text" name="username" placeholder="e.g. admin" required>
                <label>Password:</label>
                <input type="password" name="password" placeholder="e.g. password123">
                <button type="submit">Log In</button>
            </form>
            <?= $login_msg ?>
        </div>

        <!-- Challenge 2 -->
        <div class="box">
            <h2>🎯 Challenge 2: UNION-Based Product Search</h2>
            <p>Can you inject into the product search bar to dump all user records and hashes from the <code>users</code> table?</p>
            <form method="GET">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search product name (e.g. Keyboard)">
                <button type="submit">Search</button>
            </form>

            <?php if ($search_query_debug): ?>
                <p style="font-size:0.85rem; color:#8b949e;">Debug Query: <code><?= htmlspecialchars($search_query_debug) ?></code></p>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price ($)</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="5">No products found matching your search.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['id'] ?? '') ?></td>
                                <td><?= htmlspecialchars($p['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($p['category'] ?? '') ?></td>
                                <td><?= htmlspecialchars($p['price'] ?? '') ?></td>
                                <td><?= htmlspecialchars($p['description'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
