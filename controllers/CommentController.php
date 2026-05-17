<?php
// ============================================================
// controllers/CommentController.php
// CONTROLLER LAYER — receives AJAX action, validates input,
// calls Model functions, returns JSON response
// NO HTML here. NO SQL here.
// ============================================================

require_once __DIR__ . '/../models/CommentModel.php';

// ────────────────────────────────────────────────────────────
// handleCommentRequest()
// Called by ajax_handler.php
// $action : 'post' | 'delete'
// $data   : $_POST array from AJAX fetch()
// $session: $_SESSION (passed in for testability)
// ────────────────────────────────────────────────────────────
function handleCommentRequest($action, $data, $session) {

    switch ($action) {

        // ── POST COMMENT ─────────────────────────────────────
        case 'post':
            // Auth check: only logged-in users can comment
            if (empty($session['user_id'])) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'You must be logged in to comment.'
                ]);
                return;
            }

            // Sanitize: trim whitespace
            $articleId = intval($data['article_id'] ?? 0);
            $body      = trim($data['body'] ?? '');

            // Validate: article_id must be positive
            if ($articleId <= 0) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid article.'
                ]);
                return;
            }

            // Validate: body must be at least 5 characters (per requirement)
            if (strlen($body) < 5) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Comment must be at least 5 characters.'
                ]);
                return;
            }

            // Call Model to insert comment
            $comment = insertComment($articleId, $session['user_id'], $body);

            if ($comment) {
                // Return the full comment object so JS can prepend it to the thread
                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Comment posted!',
                    'comment' => $comment  // JS uses this to build the HTML card
                ]);
            } else {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Failed to post comment. Try again.'
                ]);
            }
            break;

        // ── DELETE COMMENT ────────────────────────────────────
        case 'delete':
            // Auth check
            if (empty($session['user_id'])) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Not authenticated.'
                ]);
                return;
            }

            $commentId = intval($data['id'] ?? 0);
            if ($commentId <= 0) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid comment ID.'
                ]);
                return;
            }

            // Authorization check:
            // Only the comment's own author OR an admin/article author can delete
            $authorId = getCommentAuthor($commentId);
            $role     = $session['role'] ?? 'user';

            // Is caller the comment author?
            $isOwner = ($authorId === $session['user_id']);
            // Is caller an admin or author role?
            $isAdminOrAuthor = in_array($role, ['admin', 'author']);

            if (!$isOwner && !$isAdminOrAuthor) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'You do not have permission to delete this comment.'
                ]);
                return;
            }

            // Call Model — CASCADE also deletes reported_comments rows
            $result = deleteComment($commentId);

            echo json_encode([
                'status'  => $result ? 'success' : 'error',
                'message' => $result ? 'Comment deleted.' : 'Delete failed.'
            ]);
            break;

        default:
            echo json_encode([
                'status'  => 'error',
                'message' => 'Unknown comment action: ' . htmlspecialchars($action)
            ]);
    }
}


// ────────────────────────────────────────────────────────────
// handleReportRequest()
// Handles reporting a comment
// $action: 'report'
// ────────────────────────────────────────────────────────────
function handleReportRequest($action, $data, $session) {

    switch ($action) {

        // ── REPORT COMMENT ────────────────────────────────────
        case 'report':
            // Auth check
            if (empty($session['user_id'])) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'You must be logged in to report a comment.'
                ]);
                return;
            }

            $commentId  = intval($data['comment_id'] ?? 0);
            $reason     = trim($data['reason'] ?? '');
            $reportedBy = $session['user_id'];

            // Validate comment ID
            if ($commentId <= 0) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid comment ID.'
                ]);
                return;
            }

            // Validate reason
            if (empty($reason)) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Please provide a reason for reporting.'
                ]);
                return;
            }

            // Call Model — INSERT IGNORE handles duplicate quietly
            $result = reportComment($commentId, $reportedBy, $reason);

            if ($result) {
                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Comment reported. Thank you.'
                ]);
            } else {
                // INSERT IGNORE returned 0 affected rows = already reported
                echo json_encode([
                    'status'  => 'already',
                    'message' => 'You have already reported this comment.'
                ]);
            }
            break;

        default:
            echo json_encode([
                'status'  => 'error',
                'message' => 'Unknown report action.'
            ]);
    }
}


// ────────────────────────────────────────────────────────────
// handleAdminRequest()
// Handles admin moderation dashboard actions
// $action: 'clear_flag' | 'delete_comment' | 'get_stats'
// ────────────────────────────────────────────────────────────
function handleAdminRequest($action, $data, $session) {

    // Admin-only guard: reject non-admins immediately
    if (($session['role'] ?? '') !== 'admin') {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Access denied. Admins only.'
        ]);
        return;
    }

    switch ($action) {

        // ── CLEAR FLAG (keep comment, remove report) ──────────
        case 'clear_flag':
            $reportId = intval($data['report_id'] ?? 0);
            if ($reportId <= 0) {
                echo json_encode(['status'=>'error','message'=>'Invalid report ID.']);
                return;
            }

            $result = clearReport($reportId);
            echo json_encode([
                'status'  => $result ? 'success' : 'error',
                'message' => $result ? 'Flag cleared.' : 'Failed to clear flag.'
            ]);
            break;

        // ── DELETE COMMENT (admin deletes comment + its report) ──
        case 'admin_delete_comment':
            $commentId = intval($data['comment_id'] ?? 0);
            if ($commentId <= 0) {
                echo json_encode(['status'=>'error','message'=>'Invalid comment ID.']);
                return;
            }

            // deleteComment() cascades to reported_comments via DB
            $result = deleteComment($commentId);
            echo json_encode([
                'status'  => $result ? 'success' : 'error',
                'message' => $result ? 'Comment and report deleted.' : 'Delete failed.'
            ]);
            break;

        // ── GET DASHBOARD STATS ────────────────────────────────
        case 'get_stats':
            $stats = getDashboardStats();
            echo json_encode(['status' => 'success', 'data' => $stats]);
            break;

        default:
            echo json_encode(['status'=>'error','message'=>'Unknown admin action.']);
    }
}
?>
