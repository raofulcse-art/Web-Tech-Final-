<?php
// ============================================================
// index.php — Router / Entry Point
// Reads ?page= from URL and loads the correct view
// ============================================================

session_start();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/CommentModel.php';

// Read which page to show (default = article list)
$page = $_GET['page'] ?? 'articles';

switch ($page) {
    case 'articles':
        require_once 'views/articles.php';
        break;

    case 'article':
        // Single article reading page with comment thread
        $articleId = intval($_GET['id'] ?? 0);
        if ($articleId <= 0) {
            header('Location: index.php?page=articles');
            exit;
        }
        // Fetch article data
        $stmt = mysqli_prepare($conn,
            "SELECT a.*, u.username AS author_name
             FROM articles a JOIN users u ON a.author_id = u.id
             WHERE a.id = ? LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, "i", $articleId);
        mysqli_stmt_execute($stmt);
        $article = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$article) {
            echo "<p>Article not found.</p>";
            exit;
        }

        // Fetch existing comments
        $comments = getCommentsByArticle($articleId);
        require_once 'views/article.php';
        break;

    case 'login':
        require_once 'views/login.php';
        break;

    case 'logout':
        session_destroy();
        header('Location: index.php?page=articles');
        exit;

    case 'admin':
        // Admin moderation dashboard — admin only
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        $reports = getAllReportedComments();
        $stats   = getDashboardStats();
        require_once 'views/admin.php';
        break;

    default:
        header('Location: index.php?page=articles');
        exit;
}
?>
