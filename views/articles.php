<?php
// ============================================================
// views/articles.php — Article List Page
// VIEW LAYER — only HTML + echo. No SQL. No logic.
// ============================================================

// Fetch all articles with author names
$result = mysqli_query($conn,
    "SELECT a.id, a.title, a.created_at, u.username AS author_name
     FROM articles a JOIN users u ON a.author_id = u.id
     ORDER BY a.created_at DESC"
);
$articles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $articles[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Articles — Comment System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- ── NAV BAR ──────────────────────────────────────────── -->
<nav class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="nav-brand">📰 ArticleHub</a>
        <div class="nav-links">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <span class="nav-user">
                    👤 <?= htmlspecialchars($_SESSION['username']) ?>
                    (<?= $_SESSION['role'] ?>)
                </span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="index.php?page=admin" class="btn btn-sm btn-warning">⚙ Admin Panel</a>
                <?php endif; ?>
                <a href="index.php?page=logout" class="btn btn-sm btn-outline">Logout</a>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-sm btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ── ARTICLE LIST ─────────────────────────────────────── -->
<div class="container" style="margin-top:30px">
    <h1 style="margin-bottom:20px">Latest Articles</h1>

    <?php if (empty($articles)): ?>
        <p>No articles yet.</p>
    <?php else: ?>
        <?php foreach ($articles as $art): ?>
            <div class="card article-card">
                <h2 class="article-title">
                    <a href="index.php?page=article&id=<?= $art['id'] ?>">
                        <?= htmlspecialchars($art['title']) ?>
                    </a>
                </h2>
                <div class="article-meta">
                    By <strong><?= htmlspecialchars($art['author_name']) ?></strong>
                    · <?= date('M j, Y', strtotime($art['created_at'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
