<?php
// ============================================================
// models/CommentModel.php
// MODEL LAYER — all DB functions for comments & reports
// NO HTML here. NO $_POST here. Only SQL functions.
// ============================================================

require_once __DIR__ . '/../config/db.php';

// ────────────────────────────────────────────────────────────
// FUNCTION 1: Insert a new comment
// Called by: CommentController → case 'post'
// Returns:   array with new comment data on success, false on fail
// ────────────────────────────────────────────────────────────
function insertComment($articleId, $userId, $body) {
    global $conn;

    // Prepared statement prevents SQL injection
    $stmt = mysqli_prepare($conn,
        "INSERT INTO comments (article_id, user_id, body) VALUES (?, ?, ?)"
    );
    if (!$stmt) return false;

    // "iis" = integer, integer, string
    mysqli_stmt_bind_param($stmt, "iis", $articleId, $userId, $body);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Get the new comment's auto-generated ID
    $newId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Fetch the full comment row so we can return it to JS
    // JS will prepend it to the comment thread
    return getCommentById($newId);
}

// ────────────────────────────────────────────────────────────
// FUNCTION 2: Get all comments for an article
// Called by: Article view page on load
// Returns:   array of comment rows with username joined
// ────────────────────────────────────────────────────────────
function getCommentsByArticle($articleId) {
    global $conn;

    // JOIN with users table to get the username alongside comment
    $stmt = mysqli_prepare($conn,
        "SELECT c.id, c.body, c.created_at, c.user_id, c.article_id,
                u.username
         FROM   comments c
         JOIN   users    u ON c.user_id = u.id
         WHERE  c.article_id = ?
         ORDER  BY c.created_at DESC"  // newest first (prepend to top)
    );
    if (!$stmt) return [];

    mysqli_stmt_bind_param($stmt, "i", $articleId);
    mysqli_stmt_execute($stmt);

    $result   = mysqli_stmt_get_result($stmt);
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }

    mysqli_stmt_close($stmt);
    return $comments;
}

// ────────────────────────────────────────────────────────────
// FUNCTION 3: Get a single comment by ID
// Used internally after insert to return full comment data
// ────────────────────────────────────────────────────────────
function getCommentById($id) {
    global $conn;

    $stmt = mysqli_prepare($conn,
        "SELECT c.id, c.body, c.created_at, c.user_id, c.article_id,
                u.username
         FROM   comments c
         JOIN   users    u ON c.user_id = u.id
         WHERE  c.id = ?
         LIMIT  1"
    );
    if (!$stmt) return null;

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result  = mysqli_stmt_get_result($stmt);
    $comment = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return $comment;  // Returns single row or null
}

// ────────────────────────────────────────────────────────────
// FUNCTION 4: Delete a comment (and its reports via CASCADE)
// Called by: CommentController → case 'delete'
// Returns:   true on success, false on failure
// ────────────────────────────────────────────────────────────
function deleteComment($commentId) {
    global $conn;

    // ON DELETE CASCADE in schema automatically removes
    // all reported_comments rows for this comment
    $stmt = mysqli_prepare($conn, "DELETE FROM comments WHERE id = ?");
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, "i", $commentId);
    $result = mysqli_stmt_execute($stmt);

    // Check if a row was actually deleted
    $affected = mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);

    return ($result && $affected > 0);
}

// ────────────────────────────────────────────────────────────
// FUNCTION 5: Get the author (user_id) of a comment
// Used to verify the caller is the comment's author
// ────────────────────────────────────────────────────────────
function getCommentAuthor($commentId) {
    global $conn;

    $stmt = mysqli_prepare($conn,
        "SELECT user_id FROM comments WHERE id = ? LIMIT 1"
    );
    if (!$stmt) return null;

    mysqli_stmt_bind_param($stmt, "i", $commentId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return $row ? $row['user_id'] : null;
}

// ────────────────────────────────────────────────────────────
// FUNCTION 6: Report a comment
// UNIQUE constraint (comment_id, reported_by) prevents duplicates
// Called by: ReportController → case 'report'
// Returns:   true on success, false if already reported or failed
// ────────────────────────────────────────────────────────────
function reportComment($commentId, $reportedBy, $reason) {
    global $conn;

    // INSERT IGNORE silently skips if the UNIQUE key already exists
    // (same user reporting the same comment twice)
    $stmt = mysqli_prepare($conn,
        "INSERT IGNORE INTO reported_comments (comment_id, reported_by, reason)
         VALUES (?, ?, ?)"
    );
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, "iis", $commentId, $reportedBy, $reason);
    $result   = mysqli_stmt_execute($stmt);
    $affected = mysqli_affected_rows($conn);

    mysqli_stmt_close($stmt);

    // affected = 0 means the UNIQUE constraint triggered (already reported)
    return ($result && $affected > 0);
}

// ────────────────────────────────────────────────────────────
// FUNCTION 7: Get all reported comments for admin dashboard
// Joins reported_comments + comments + articles + users
// ────────────────────────────────────────────────────────────
function getAllReportedComments() {
    global $conn;

    $result = mysqli_query($conn,
        "SELECT rc.id        AS report_id,
                rc.reason,
                rc.created_at AS reported_at,
                c.id         AS comment_id,
                c.body       AS comment_body,
                a.title      AS article_title,
                reporter.username AS reporter_name,
                author.username   AS comment_author
         FROM   reported_comments rc
         JOIN   comments c   ON rc.comment_id  = c.id
         JOIN   articles a   ON c.article_id   = a.id
         JOIN   users reporter ON rc.reported_by = reporter.id
         JOIN   users author   ON c.user_id      = author.id
         ORDER  BY rc.created_at DESC"
    );

    $reports = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reports[] = $row;
    }
    return $reports;
}

// ────────────────────────────────────────────────────────────
// FUNCTION 8: Delete only the report row (keep comment)
// Admin "Clear Flag" action
// ────────────────────────────────────────────────────────────
function clearReport($reportId) {
    global $conn;

    $stmt = mysqli_prepare($conn,
        "DELETE FROM reported_comments WHERE id = ?"
    );
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, "i", $reportId);
    $result   = mysqli_stmt_execute($stmt);
    $affected = mysqli_affected_rows($conn);

    mysqli_stmt_close($stmt);
    return ($result && $affected > 0);
}

// ────────────────────────────────────────────────────────────
// FUNCTION 9: Get dashboard summary stats for admin bar
// Returns total articles, total comments, total flagged
// ────────────────────────────────────────────────────────────
function getDashboardStats() {
    global $conn;

    $articles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM articles"))['n'];
    $comments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM comments"))['n'];
    $flagged  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM reported_comments"))['n'];

    return [
        'total_articles' => $articles,
        'total_comments' => $comments,
        'total_flagged'  => $flagged,
    ];
}
?>
