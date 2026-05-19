<!DOCTYPE html>
<html>
<head>
  <title>DB Test</title>
  <style>
    body { font-family: sans-serif; padding: 2rem; background: #f4f4f8; }
    .ok  { color: #2a9d4a; font-weight: bold; }
    .err { color: #e84855; font-weight: bold; }
    table { border-collapse: collapse; margin-top: 1rem; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    th, td { padding: .5rem 1rem; border-bottom: 1px solid #eee; text-align: left; font-size: .9rem; }
    th { background: #f0f0f5; }
  </style>
</head>
<body>
  <h2>Task 3 — Database Connection Test</h2>

<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();
    echo '<p class="ok">✓ Database connected successfully!</p>';

    // Check tables
    $tables = ['users','categories','tags','articles','article_tags','likes'];
    echo '<h3 style="margin-top:1.5rem">Table check</h3>';
    echo '<table><tr><th>Table</th><th>Rows</th><th>Status</th></tr>';
    foreach ($tables as $t) {
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            echo "<tr><td>$t</td><td>$count</td><td class='ok'>✓ exists</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>$t</td><td>—</td><td class='err'>✗ missing</td></tr>";
        }
    }
    echo '</table>';

    // Quick query test
    $articles = $pdo->query("SELECT id, title, status FROM articles LIMIT 5")->fetchAll();
    echo '<h3 style="margin-top:1.5rem">Sample articles</h3>';
    if ($articles) {
        echo '<table><tr><th>ID</th><th>Title</th><th>Status</th></tr>';
        foreach ($articles as $a) {
            echo "<tr><td>{$a['id']}</td><td>{$a['title']}</td><td>{$a['status']}</td></tr>";
        }
        echo '</table>';
    } else {
        echo '<p style="color:#888">No articles found — run setup.sql first.</p>';
    }

} catch (Exception $e) {
    echo '<p class="err">✗ Connection failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Check that XAMPP MySQL is running and the database name in <code>config/database.php</code> is correct.</p>';
}
?>

<p style="margin-top:2rem">
  <a href="/task3/controllers/HomeController.php" style="color:#5b4cf5">→ Go to homepage</a>
</p>
</body>
</html>
