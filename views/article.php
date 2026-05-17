<?php
// ============================================================
// views/article.php — Single Article + Comment Thread
// VIEW LAYER — HTML + echo + AJAX JavaScript
// Variables available: $article (array), $comments (array),
//                      $articleId (int), $_SESSION
// ============================================================
$loggedIn  = !empty($_SESSION['user_id']);
$userId    = $_SESSION['user_id']  ?? 0;
$userRole  = $_SESSION['role']     ?? 'guest';
$username  = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['title']) ?> — ArticleHub</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- ── NAV BAR ──────────────────────────────────────────── -->
<nav class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="nav-brand">📰 ArticleHub</a>
        <div class="nav-links">
            <?php if ($loggedIn): ?>
                <span class="nav-user">👤 <?= htmlspecialchars($username) ?> (<?= $userRole ?>)</span>
                <?php if ($userRole === 'admin'): ?>
                    <a href="index.php?page=admin" class="btn btn-sm btn-warning">⚙ Admin Panel</a>
                <?php endif; ?>
                <a href="index.php?page=logout" class="btn btn-sm btn-outline">Logout</a>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-sm btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container" style="margin-top:30px">

    <!-- ── ARTICLE BODY ─────────────────────────────────── -->
    <div class="card" style="margin-bottom:28px">
        <div class="article-meta" style="margin-bottom:8px">
            <a href="index.php">← All Articles</a>
        </div>
        <h1 style="margin-bottom:8px"><?= htmlspecialchars($article['title']) ?></h1>
        <div class="article-meta">
            By <strong><?= htmlspecialchars($article['author_name']) ?></strong>
            · <?= date('M j, Y', strtotime($article['created_at'])) ?>
        </div>
        <hr style="margin:16px 0;border:none;border-top:1px solid #eee">
        <div class="article-body">
            <?= nl2br(htmlspecialchars($article['body'])) ?>
        </div>
    </div>

    <!-- ── COMMENT SECTION ──────────────────────────────── -->
    <div class="section-title">💬 Comments</div>

    <!-- Flash message box (shown/hidden by JS) -->
    <div id="flash-msg" class="alert" style="display:none"></div>

    <!-- ── POST COMMENT FORM ── -->
    <?php if ($loggedIn): ?>
        <div class="card" style="margin-bottom:20px">
            <h3 style="margin-bottom:12px">Post a Comment</h3>
            <textarea id="comment-body"
                      placeholder="Write your comment here (min 5 characters)..."
                      rows="4"></textarea>
            <!-- Hidden: article ID passed to AJAX -->
            <input type="hidden" id="article-id" value="<?= $articleId ?>">
            <button class="btn btn-primary" onclick="postComment()" style="margin-top:10px">
                Post Comment
            </button>
        </div>
    <?php else: ?>
        <!-- Guests see login link instead of form -->
        <div class="card login-prompt">
            <a href="index.php?page=login">Login to comment</a>
        </div>
    <?php endif; ?>

    <!-- ── COMMENT THREAD ── -->
    <!-- JS prepends new comments to top of this list -->
    <div id="comment-thread">

        <?php if (empty($comments)): ?>
            <p id="no-comments" style="color:#888;text-align:center;padding:20px">
                No comments yet. Be the first!
            </p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <?php
                // Can this user delete this comment?
                $canDelete = $loggedIn && (
                    $c['user_id'] == $userId ||
                    in_array($userRole, ['admin', 'author'])
                );
                ?>
                <!-- Each comment card has id="comment-{id}" for JS targeting -->
                <div class="comment-card" id="comment-<?= $c['id'] ?>">
                    <div class="comment-header">
                        <span class="comment-author">
                            👤 <?= htmlspecialchars($c['username']) ?>
                        </span>
                        <span class="comment-date">
                            <?= date('M j, Y g:i a', strtotime($c['created_at'])) ?>
                        </span>
                    </div>

                    <!-- Comment body text -->
                    <div class="comment-body">
                        <?= nl2br(htmlspecialchars($c['body'])) ?>
                    </div>

                    <!-- Action links row -->
                    <div class="comment-actions">
                        <?php if ($loggedIn): ?>

                            <!-- Report link — only for logged-in, not own comment -->
                            <?php if ($c['user_id'] != $userId): ?>
                                <span class="report-section" id="report-section-<?= $c['id'] ?>">
                                    <!-- Clicking shows inline reason form -->
                                    <a href="#" class="link-report"
                                       onclick="showReportForm(<?= $c['id'] ?>); return false;">
                                        🚩 Report
                                    </a>
                                    <!-- Inline reason form (hidden by default) -->
                                    <span class="report-form" id="report-form-<?= $c['id'] ?>" style="display:none">
                                        <input type="text"
                                               id="report-reason-<?= $c['id'] ?>"
                                               placeholder="Reason for reporting..."
                                               class="report-input">
                                        <button class="btn btn-sm btn-danger"
                                                onclick="submitReport(<?= $c['id'] ?>)">
                                            Submit
                                        </button>
                                        <button class="btn btn-sm btn-outline"
                                                onclick="hideReportForm(<?= $c['id'] ?>)">
                                            Cancel
                                        </button>
                                    </span>
                                </span>
                            <?php endif; ?>

                            <!-- Delete link — only for owner or admin/author -->
                            <?php if ($canDelete): ?>
                                <a href="#" class="link-delete"
                                   onclick="deleteComment(<?= $c['id'] ?>); return false;">
                                    🗑 Delete
                                </a>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div><!-- end #comment-thread -->

</div><!-- end .container -->

<!-- ── JAVASCRIPT / AJAX ─────────────────────────────────── -->
<script>
// Pass PHP session data to JS (used for permission checks client-side)
const SESSION = {
    loggedIn : <?= $loggedIn ? 'true' : 'false' ?>,
    userId   : <?= $userId ?>,
    userRole : "<?= $userRole ?>"
};

// ── UTILITY: Show flash message ───────────────────────────
function showFlash(msg, type) {
    const el = document.getElementById('flash-msg');
    el.textContent    = msg;
    el.className      = 'alert alert-' + type;
    el.style.display  = 'block';
    // Auto-hide after 4 seconds
    setTimeout(() => el.style.display = 'none', 4000);
}

// ── UTILITY: Build comment HTML card from a comment object ─
// Called after AJAX post — JS builds and PREPENDS the new card
function buildCommentCard(c) {
    // Escape HTML to prevent XSS when injecting user text into DOM
    const escHtml = str => {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    };

    // canDelete client-side check (server also validates)
    const canDelete = SESSION.loggedIn &&
        (c.user_id == SESSION.userId || ['admin','author'].includes(SESSION.userRole));

    // canReport — logged-in users who are not the comment author
    const canReport = SESSION.loggedIn && c.user_id != SESSION.userId;

    // Report actions HTML
    const reportHtml = canReport ? `
        <span class="report-section" id="report-section-${c.id}">
            <a href="#" class="link-report"
               onclick="showReportForm(${c.id}); return false;">🚩 Report</a>
            <span class="report-form" id="report-form-${c.id}" style="display:none">
                <input type="text" id="report-reason-${c.id}"
                       placeholder="Reason for reporting..." class="report-input">
                <button class="btn btn-sm btn-danger"
                        onclick="submitReport(${c.id})">Submit</button>
                <button class="btn btn-sm btn-outline"
                        onclick="hideReportForm(${c.id})">Cancel</button>
            </span>
        </span>` : '';

    const deleteHtml = canDelete
        ? `<a href="#" class="link-delete" onclick="deleteComment(${c.id}); return false;">🗑 Delete</a>`
        : '';

    return `
        <div class="comment-card" id="comment-${c.id}">
            <div class="comment-header">
                <span class="comment-author">👤 ${escHtml(c.username)}</span>
                <span class="comment-date">Just now</span>
            </div>
            <div class="comment-body">${escHtml(c.body)}</div>
            <div class="comment-actions">${reportHtml}${deleteHtml}</div>
        </div>`;
}

// ════════════════════════════════════════════════════════════
// OPERATION 1: POST COMMENT
// Sends: article_id + body via POST
// On success: prepends new comment card to thread
// ════════════════════════════════════════════════════════════
function postComment() {
    const body      = document.getElementById('comment-body').value.trim();
    const articleId = document.getElementById('article-id').value;

    // Client-side validation (server also validates)
    if (body.length < 5) {
        showFlash('Comment must be at least 5 characters.', 'error');
        return;
    }

    const fd = new FormData();
    fd.append('action',     'post');
    fd.append('article_id', articleId);
    fd.append('body',       body);

    fetch('ajax_handler.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showFlash(res.message, res.status === 'success' ? 'success' : 'error');

            if (res.status === 'success') {
                // Clear textarea
                document.getElementById('comment-body').value = '';

                // Hide "No comments yet" message if visible
                const noMsg = document.getElementById('no-comments');
                if (noMsg) noMsg.style.display = 'none';

                // PREPEND new comment to TOP of the thread (newest first)
                const thread = document.getElementById('comment-thread');
                thread.insertAdjacentHTML('afterbegin', buildCommentCard(res.comment));
            }
        })
        .catch(err => showFlash('Request failed: ' + err.message, 'error'));
}

// ════════════════════════════════════════════════════════════
// OPERATION 2: SHOW / HIDE REPORT FORM
// Toggles the inline reason input below a comment
// ════════════════════════════════════════════════════════════
function showReportForm(commentId) {
    document.getElementById('report-form-' + commentId).style.display = 'inline';
    // Hide the "Report" link so it's replaced by the form
    const link = document.querySelector(`#report-section-${commentId} .link-report`);
    if (link) link.style.display = 'none';
}

function hideReportForm(commentId) {
    document.getElementById('report-form-' + commentId).style.display = 'none';
    const link = document.querySelector(`#report-section-${commentId} .link-report`);
    if (link) link.style.display = 'inline';
    // Clear the reason input
    document.getElementById('report-reason-' + commentId).value = '';
}

// ════════════════════════════════════════════════════════════
// OPERATION 3: SUBMIT REPORT
// Sends: comment_id + reason via POST
// On success: replaces report link with "Reported ✓"
// ════════════════════════════════════════════════════════════
function submitReport(commentId) {
    const reason = document.getElementById('report-reason-' + commentId).value.trim();

    if (!reason) {
        showFlash('Please enter a reason for reporting.', 'error');
        return;
    }

    const fd = new FormData();
    fd.append('action',     'report');
    fd.append('comment_id', commentId);
    fd.append('reason',     reason);

    fetch('ajax_handler.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showFlash(res.message, res.status === 'success' ? 'success' : 'error');

            if (res.status === 'success' || res.status === 'already') {
                // Replace entire report section with "Reported ✓" text
                const section = document.getElementById('report-section-' + commentId);
                if (section) {
                    section.innerHTML = '<span class="reported-badge">Reported ✓</span>';
                }
            }
        })
        .catch(err => showFlash('Report failed: ' + err.message, 'error'));
}

// ════════════════════════════════════════════════════════════
// OPERATION 4: DELETE COMMENT
// Sends: id via POST (DELETE verb emulated via action param)
// On success: fades out and removes the comment card from DOM
// ════════════════════════════════════════════════════════════
function deleteComment(commentId) {
    if (!confirm('Delete this comment? This cannot be undone.')) return;

    const fd = new FormData();
    fd.append('action', 'delete');
    fd.append('id',     commentId);

    fetch('ajax_handler.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showFlash(res.message, res.status === 'success' ? 'success' : 'error');

            if (res.status === 'success') {
                // Fade out the comment card then remove from DOM
                const card = document.getElementById('comment-' + commentId);
                if (card) {
                    card.style.transition = 'opacity 0.4s, transform 0.4s';
                    card.style.opacity    = '0';
                    card.style.transform  = 'translateX(20px)';
                    setTimeout(() => card.remove(), 400);
                }
            }
        })
        .catch(err => showFlash('Delete failed: ' + err.message, 'error'));
}
</script>

</body>
</html>
