<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/Article.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Minimum 2 characters to search
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$results = Article::search($q);
echo json_encode($results);
