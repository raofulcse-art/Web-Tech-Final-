<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../models/Like.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Login required to like articles']);
    exit;
}

// Read JSON body sent from JS fetch()
$data       = json_decode(file_get_contents('php://input'), true);
$article_id = isset($data['article_id']) ? (int) $data['article_id'] : 0;

if (!$article_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid article ID']);
    exit;
}

$result = Like::toggle($article_id, $_SESSION['user_id']);
echo json_encode($result);  // {"liked": true, "count": 5}
