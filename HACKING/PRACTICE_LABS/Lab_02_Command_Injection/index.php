<?php
// Lab 02: OS Command Injection
// A vulnerable network diagnostics utility

$output = '';
$command_executed = '';
$flag = "FLAG{CMD_INJECTION_RCE_ROOT_PWNED}";

// Create a simulated secret file on disk if it doesn't exist
$secret_file = __DIR__ . '/secret_flag.txt';
if (!file_exists($secret_file)) {
    file_put_contents($secret_file, $flag . "\nCongratulations on achieving Remote Command Execution!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip'])) {
    $ip = trim($_POST['ip']);
    
    // Level 1: Weak Blacklist Filter
    // Developer attempted to block "&&" and "bash"
    if (strpos($ip, '&&') !== false) {
        $output = "❌ Security Alert: '&&' is blocked by WAF!";
    } elseif (stripos($ip, 'bash') !== false) {
        $output = "❌ Security Alert: 'bash' keyword is prohibited!";
    } else {
        // Cross-platform ping command (Windows -n 2, Linux -c 2)
        $is_win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $ping_cmd = $is_win ? "ping -n 1 " : "ping -c 1 ";
        
        // VULNERABILITY: Raw command concatenation into shell execution
        $cmd = $ping_cmd . $ip;
        $command_executed = $cmd;
        
        // Execute and capture output
        $output = shell_exec($cmd . " 2>&1");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab 02: OS Command Injection</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #0d1117; color: #c9d1d9; padding: 2rem; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2 { color: #d29922; }
        .box { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; }
        input[type="text"] { background: #0d1117; border: 1px solid #30363d; color: #fff; padding: 0.6rem 0.8rem; border-radius: 6px; width: 75%; box-sizing: border-box; }
        button { background: #d29922; color: #000; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #e3b341; }
        pre { background: #000; color: #39ff14; padding: 1rem; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 0.9rem; border: 1px solid #30363d; }
        code { background: #21262d; color: #ff7b72; padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; }
        a { color: #58a6ff; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="../">← Back to Practice Labs Dashboard</a> | <a href="LAB_WALKTHROUGH.md" target="_blank">📖 Open Lab Walkthrough</a></p>
        <h1>⚡ Lab 02: OS Command Injection (RCE)</h1>
        <p>This utility allows administrators to ping a network host or server IP. Can you break out of the ping command and read <code>secret_flag.txt</code> from the server filesystem?</p>

        <div class="box">
            <h2>🛠️ Server Diagnostics: Ping Utility</h2>
            <form method="POST">
                <input type="text" name="ip" value="<?= htmlspecialchars($_POST['ip'] ?? '127.0.0.1') ?>" placeholder="Enter IP or hostname">
                <button type="submit">Execute Ping</button>
            </form>

            <?php if ($command_executed): ?>
                <p style="font-size:0.85rem; color:#8b949e; margin-top:1rem;">Command Executed: <code><?= htmlspecialchars($command_executed) ?></code></p>
            <?php endif; ?>

            <?php if ($output !== ''): ?>
                <h3>Execution Terminal Output:</h3>
                <pre><?= htmlspecialchars($output) ?></pre>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
