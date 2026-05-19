<?php
session_start();
require_once __DIR__ . '/../models/Article.php';
require_once __DIR__ . '/../models/Like.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    header('Location: /task3/controllers/HomeController.php');
    exit;
}

$article = Article::getById($id);

if (!$article) {
    http_response_code(404);
    die('<h2>Article not found.</h2><a href="/task3/controllers/HomeController.php">← Back home</a>');
}

Article::incrementViews($id);

$tags = Article::getTags($id);

$userLiked = false;
if (isset($_SESSION['user_id'])) {
    $userLiked = Like::hasLiked($id, $_SESSION['user_id']);
}

require_once __DIR__ . '/../views/article.php';
