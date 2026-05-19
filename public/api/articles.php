<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/Article.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
$articles    = Article::getPublished($category_id);

echo json_encode($articles);
