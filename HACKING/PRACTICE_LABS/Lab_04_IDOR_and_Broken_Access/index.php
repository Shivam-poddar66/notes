<?php
// Lab 04: Insecure Direct Object References (IDOR)
session_start();

// Mock database of users and invoices
$users = [
    'alice' => ['id' => 1, 'role' => 'user', 'name' => 'Alice Cooper'],
    'bob'   => ['id' => 2, 'role' => 'user', 'name' => 'Bob Marley'],
    'admin' => ['id' => 99, 'role' => 'administrator', 'name' => 'Root Admin']
];

$invoices = [
    101 => ['owner' => 'alice', 'title' => 'Cloud Hosting Monthly Bill', 'amount' => '$120.00', 'status' => 'Paid'],
    102 => ['owner' => 'alice', 'title' => 'Domain Name Renewal (.com)', 'amount' => '$15.00', 'status' => 'Paid'],
    201 => ['owner' => 'bob', 'title' => 'Office Supplies - Ergonomic Chair', 'amount' => '$350.00', 'status' => 'Pending'],
    999 => [
        'owner' => 'admin',
        'title' => 'TOP SECRET: Defense Contract & Acquisition',
        'amount' => '$5,000,000.00',
        'status' => 'CONFIDENTIAL',
        'flag' => 'FLAG{IDOR_ACCESS_CONTROL_LEAK_BILLIONAIRE}'
    ]
];

// Handle Auto-login as Alice
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = 'alice';
}

$current_user = $_SESSION['user'];
$selected_invoice = null;
$view_id = $_GET['invoice_id'] ?? null;

if ($view_id !== null) {
    $view_id = (int)$view_id;
    // VULNERABILITY: Fetching directly by ID without verifying if $invoices[$view_id]['owner'] === $current_user
    if (isset($invoices[$view_id])) {
        $selected_invoice = $invoices[$view_id];
        $selected_invoice['id'] = $view_id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab 04: Insecure Direct Object References (IDOR)</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #0d1117; color: #c9d1d9; padding: 2rem; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2 { color: #3fb950; }
        .box { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; }
        .badge { background: #238636; color: #fff; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.8rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #30363d; padding: 0.6rem 0.8rem; text-align: left; }
        th { background: #21262d; color: #3fb950; }
        a { color: #58a6ff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .invoice-box { background: #0d1117; border: 1px solid #30363d; border-radius: 6px; padding: 1.5rem; margin-top: 1.5rem; }
        code { background: #21262d; color: #ff7b72; padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="../">← Back to Practice Labs Dashboard</a> | <a href="LAB_WALKTHROUGH.md" target="_blank">📖 Open Lab Walkthrough</a></p>
        <h1>🧾 Lab 04: IDOR & Broken Access Control</h1>
        <p>Logged in as: <strong><?= htmlspecialchars($current_user) ?></strong> <span class="badge">Standard User</span></p>

        <div class="box">
            <h2>Your Authorized Invoices</h2>
            <p>Here are the invoices registered to your personal account:</p>
            <table>
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Document Title</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $id => $inv): ?>
                        <?php if ($inv['owner'] === $current_user): ?>
                            <tr>
                                <td>#<?= $id ?></td>
                                <td><?= htmlspecialchars($inv['title']) ?></td>
                                <td><?= htmlspecialchars($inv['amount']) ?></td>
                                <td><?= htmlspecialchars($inv['status']) ?></td>
                                <td><a href="?invoice_id=<?= $id ?>">View Invoice</a></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($selected_invoice): ?>
                <div class="invoice-box">
                    <h3 style="color:#58a6ff; margin-top:0;">📄 Invoice Details (Document #<?= $selected_invoice['id'] ?>)</h3>
                    <p><strong>Title:</strong> <?= htmlspecialchars($selected_invoice['title']) ?></p>
                    <p><strong>Owner:</strong> <?= htmlspecialchars($selected_invoice['owner']) ?></p>
                    <p><strong>Total:</strong> <?= htmlspecialchars($selected_invoice['amount']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($selected_invoice['status']) ?></p>
                    <?php if (isset($selected_invoice['flag'])): ?>
                        <div style="background:rgba(46,160,67,0.2); border:1px solid #2ea043; padding:1rem; border-radius:6px; color:#3fb950; margin-top:1rem;">
                            🎉 <strong>IDOR Exploit Successful!</strong><br>
                            Flag: <code><?= htmlspecialchars($selected_invoice['flag']) ?></code>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
