<?php
// ============================================================
// ajax_handler.php
// AJAX BRIDGE — ALL fetch() calls from JavaScript come here
// Sets JSON header, reads action, routes to correct controller
// ============================================================

// Must be set BEFORE any echo — tells browser response is JSON
header('Content-Type: application/json');

// Start session to access $_SESSION in controllers
session_start();

// Load controller (which loads model + db.php)
require_once __DIR__ . '/controllers/CommentController.php';

// Determine request method and data source
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Read action and data from POST or GET
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    $data   = $_POST;
} else {
    $action = $_GET['action'] ?? '';
    $data   = $_GET;
}

// Guard: action must be provided
if (empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified.']);
    exit;
}

// ── Route to correct handler based on action prefix ──────────
// Comment actions: post, delete
// Report actions:  report
// Admin actions:   clear_flag, admin_delete_comment, get_stats

switch ($action) {
    case 'post':
    case 'delete':
        handleCommentRequest($action, $data, $_SESSION);
        break;

    case 'report':
        handleReportRequest($action, $data, $_SESSION);
        break;

    case 'clear_flag':
    case 'admin_delete_comment':
    case 'get_stats':
        handleAdminRequest($action, $data, $_SESSION);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
}
?>
