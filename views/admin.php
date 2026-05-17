<?php
// ============================================================
// views/admin.php — Admin Moderation Dashboard
// VIEW LAYER — Shows all reported comments
// Variables: $reports (array), $stats (array)
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin — Moderation Dashboard</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- ── NAV BAR ──────────────────────────────────────────── -->
<nav class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="nav-brand">📰 ArticleHub</a>
        <div class="nav-links">
            <span class="nav-user">⚙ Admin: <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="index.php?page=articles" class="btn btn-sm btn-outline">Articles</a>
            <a href="index.php?page=logout"   class="btn btn-sm btn-outline">Logout</a>
        </div>
    </div>
</nav>

<div class="container" style="margin-top:30px">
    <h1 style="margin-bottom:6px">⚙ Moderation Dashboard</h1>
    <p style="color:#666;margin-bottom:24px">Review and action flagged comments.</p>

    <!-- ── STATS SUMMARY BAR ── -->
    <div class="stats-bar" id="stats-bar">
        <div class="stat-box">
            <div class="stat-num" id="stat-articles"><?= $stats['total_articles'] ?></div>
            <div class="stat-label">Published Articles</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" id="stat-comments"><?= $stats['total_comments'] ?></div>
            <div class="stat-label">Total Comments</div>
        </div>
        <div class="stat-box stat-danger">
            <div class="stat-num" id="stat-flagged"><?= $stats['total_flagged'] ?></div>
            <div class="stat-label">Flagged Comments</div>
        </div>
    </div>

    <!-- Flash message -->
    <div id="flash-msg" class="alert" style="display:none;margin:16px 0"></div>

    <!-- ── FLAGGED COMMENTS TABLE ── -->
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:16px 20px;border-bottom:1px solid #eee">
            <h2 style="font-size:16px">🚩 Flagged Comments (<?= count($reports) ?>)</h2>
        </div>

        <?php if (empty($reports)): ?>
            <p style="padding:30px;text-align:center;color:#888">
                ✅ No flagged comments. All clear!
            </p>
        <?php else: ?>
            <table class="admin-table" id="reports-table">
                <thead>
                    <tr>
                        <th>Comment</th>
                        <th>Article</th>
                        <th>Author</th>
                        <th>Reported By</th>
                        <th>Reason</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $r): ?>
                        <!-- Each row has id="report-row-{report_id}" for JS removal -->
                        <tr id="report-row-<?= $r['report_id'] ?>">
                            <td class="comment-cell">
                                "<?= htmlspecialchars(substr($r['comment_body'], 0, 80)) ?>
                                <?= strlen($r['comment_body']) > 80 ? '...' : '' ?>"
                            </td>
                            <td><?= htmlspecialchars($r['article_title']) ?></td>
                            <td><?= htmlspecialchars($r['comment_author']) ?></td>
                            <td><?= htmlspecialchars($r['reporter_name']) ?></td>
                            <td class="reason-cell"><?= htmlspecialchars($r['reason']) ?></td>
                            <td style="white-space:nowrap;font-size:12px">
                                <?= date('M j, Y', strtotime($r['reported_at'])) ?>
                            </td>
                            <td class="action-cell">
                                <!-- Clear Flag: removes report row, keeps comment -->
                                <button class="btn btn-sm btn-outline"
                                        onclick="clearFlag(<?= $r['report_id'] ?>)">
                                    ✅ Clear Flag
                                </button>
                                <!-- Delete Comment: removes comment + its report -->
                                <button class="btn btn-sm btn-danger"
                                        onclick="adminDeleteComment(<?= $r['comment_id'] ?>, <?= $r['report_id'] ?>)">
                                    🗑 Delete Comment
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div><!-- end .container -->

<!-- ── JAVASCRIPT / AJAX ─────────────────────────────────── -->
<script>
// ── UTILITY: Show flash message ───────────────────────────
function showFlash(msg, type) {
    const el = document.getElementById('flash-msg');
    el.textContent   = msg;
    el.className     = 'alert alert-' + type;
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 4000);
}

// ── UTILITY: Fade out and remove a table row ──────────────
function removeRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.style.transition  = 'opacity 0.4s';
        row.style.opacity     = '0';
        setTimeout(() => row.remove(), 400);
    }
}

// ── UTILITY: Update stat numbers after actions ────────────
function refreshStats() {
    fetch('ajax_handler.php?action=get_stats')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                document.getElementById('stat-articles').textContent = res.data.total_articles;
                document.getElementById('stat-comments').textContent = res.data.total_comments;
                document.getElementById('stat-flagged').textContent  = res.data.total_flagged;
            }
        });
}

// ════════════════════════════════════════════════════════════
// ADMIN ACTION 1: CLEAR FLAG
// Deletes the reported_comments row only — comment stays
// On success: fades out the table row
// ════════════════════════════════════════════════════════════
function clearFlag(reportId) {
    if (!confirm('Clear this flag? The comment will remain.')) return;

    const fd = new FormData();
    fd.append('action',    'clear_flag');
    fd.append('report_id', reportId);

    fetch('ajax_handler.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showFlash(res.message, res.status === 'success' ? 'success' : 'error');

            if (res.status === 'success') {
                // Remove the row from the table
                removeRow('report-row-' + reportId);
                // Update stats summary bar
                refreshStats();
            }
        })
        .catch(err => showFlash('Error: ' + err.message, 'error'));
}

// ════════════════════════════════════════════════════════════
// ADMIN ACTION 2: DELETE COMMENT (from moderation panel)
// Deletes the comment (CASCADE also removes its reports)
// On success: fades out the table row
// ════════════════════════════════════════════════════════════
function adminDeleteComment(commentId, reportId) {
    if (!confirm('Permanently delete this comment and its report?')) return;

    const fd = new FormData();
    fd.append('action',     'admin_delete_comment');
    fd.append('comment_id', commentId);

    fetch('ajax_handler.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showFlash(res.message, res.status === 'success' ? 'success' : 'error');

            if (res.status === 'success') {
                // Remove row using the report_id (row ID in table)
                removeRow('report-row-' + reportId);
                // Refresh stat counters
                refreshStats();
            }
        })
        .catch(err => showFlash('Error: ' + err.message, 'error'));
}
</script>

</body>
</html>
