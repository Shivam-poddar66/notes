<?php
// Central Dashboard for Local Offensive Security Labs
$labs = [
    [
        'id' => '01',
        'title' => 'Lab 01: SQL Injection (SQLi)',
        'dir' => 'Lab_01_SQL_Injection',
        'difficulty' => 'Easy -> Medium',
        'badge' => 'badge-danger',
        'desc' => 'Vulnerable authentication form and product search. Practice auth bypass and UNION-based database extraction using SQLite.'
    ],
    [
        'id' => '02',
        'title' => 'Lab 02: OS Command Injection (RCE)',
        'dir' => 'Lab_02_Command_Injection',
        'difficulty' => 'Medium',
        'badge' => 'badge-warning',
        'desc' => 'Vulnerable server ping tool. Break out of system commands, bypass input blacklists, and execute arbitrary commands.'
    ],
    [
        'id' => '03',
        'title' => 'Lab 03: Cross-Site Scripting (XSS)',
        'dir' => 'Lab_03_XSS_and_DOM',
        'difficulty' => 'Easy -> Medium',
        'badge' => 'badge-primary',
        'desc' => 'Reflected search query and Stored Guestbook. Craft script payloads, bypass attribute escapes, and exfiltrate simulated cookies.'
    ],
    [
        'id' => '04',
        'title' => 'Lab 04: Insecure Direct Object References (IDOR)',
        'dir' => 'Lab_04_IDOR_and_Broken_Access',
        'difficulty' => 'Easy',
        'badge' => 'badge-success',
        'desc' => 'Customer invoice & profile portal. Tamper with request parameters to access sensitive documents belonging to other users.'
    ],
    [
        'id' => '05',
        'title' => 'Lab 05: Local File Inclusion (LFI)',
        'dir' => 'Lab_05_LFI_and_Path_Traversal',
        'difficulty' => 'Medium',
        'badge' => 'badge-warning',
        'desc' => 'Dynamic page template loader. Traverse directories with ../ and use PHP stream wrappers (php://filter) to read source code.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offensive Security Practice Labs Platform</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --border-color: #30363d;
            --text-main: #c9d1d9;
            --text-bright: #f0f6fc;
            --accent-green: #2ea043;
            --accent-red: #da3633;
            --accent-blue: #58a6ff;
            --accent-orange: #d29922;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 2rem;
            line-height: 1.6;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        header {
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        h1 {
            color: var(--accent-green);
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-family: monospace;
        }
        .subtitle {
            color: #8b949e;
            font-size: 1.1rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.15s ease, border-color 0.15s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            border-color: var(--accent-blue);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        .card-title {
            color: var(--text-bright);
            font-size: 1.3rem;
            margin: 0;
            font-weight: 600;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-danger { background-color: rgba(218, 54, 51, 0.2); color: #f85149; border: 1px solid #da3633; }
        .badge-warning { background-color: rgba(210, 153, 34, 0.2); color: #e3b341; border: 1px solid #d29922; }
        .badge-success { background-color: rgba(46, 160, 67, 0.2); color: #3fb950; border: 1px solid #2ea043; }
        .badge-primary { background-color: rgba(88, 166, 255, 0.2); color: #79c0ff; border: 1px solid #58a6ff; }
        .card-desc {
            color: #8b949e;
            font-size: 0.95rem;
            margin-bottom: 1.2rem;
        }
        .btn-group {
            display: flex;
            gap: 0.8rem;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-launch {
            background-color: var(--accent-green);
            color: white;
            flex: 1;
        }
        .btn-launch:hover {
            background-color: #2c974b;
        }
        .btn-docs {
            background-color: #21262d;
            color: var(--text-bright);
            border: 1px solid var(--border-color);
        }
        .btn-docs:hover {
            background-color: #30363d;
        }
        .footer-banner {
            margin-top: 3rem;
            background-color: #1f242c;
            border-left: 4px solid var(--accent-green);
            padding: 1.2rem;
            border-radius: 4px;
        }
        code {
            background: #21262d;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #79c0ff;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>⚡ OFFENSIVE SECURITY PRACTICE LABS ⚡</h1>
            <p class="subtitle">Offline, safe, standalone practice targets to train your exploitation muscle memory.</p>
        </header>

        <div class="grid">
            <?php foreach ($labs as $lab): ?>
                <div class="card">
                    <div>
                        <div class="card-header">
                            <h2 class="card-title"><?= htmlspecialchars($lab['title']) ?></h2>
                            <span class="badge <?= $lab['badge'] ?>"><?= htmlspecialchars($lab['difficulty']) ?></span>
                        </div>
                        <p class="card-desc"><?= htmlspecialchars($lab['desc']) ?></p>
                    </div>
                    <div class="btn-group">
                        <a href="<?= $lab['dir'] ?>/" class="btn btn-launch" target="_blank">🚀 Launch Target</a>
                        <a href="<?= $lab['dir'] ?>/LAB_WALKTHROUGH.md" class="btn btn-docs" target="_blank">📖 Walkthrough</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="footer-banner">
            <h3 style="margin-top:0; color:var(--text-bright);">💡 How to Practice</h3>
            <p style="margin-bottom:0.5rem;">
                1. Click <strong>Launch Target</strong> to open the challenge in a new tab.<br>
                2. Attempt to exploit the vulnerability manually using your browser or terminal.<br>
                3. If you get stuck, open the <strong>Walkthrough</strong> for hints, PoC scripts, and the source code fix.<br>
                4. Every lab contains a secret flag formatted as <code>FLAG{...}</code>.
            </p>
        </div>
    </div>
</body>
</html>
