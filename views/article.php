<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($article['title']) ?> — BlogPress</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/task3/public/css/style.css">
</head>
<body>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="main article-main">

  <div class="article-container">

    <!-- Back link -->
    <a href="/task3/controllers/HomeController.php" class="back-link">
      <svg viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Back to all articles
    </a>

    <!-- Category badge -->
    <?php if (!empty($article['category_name'])): ?>
      <span class="badge" style="margin-bottom:1rem;display:inline-block">
        <?= htmlspecialchars($article['category_name']) ?>
      </span>
    <?php endif; ?>

    <!-- Title -->
    <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>

    <!-- Author + meta row -->
    <div class="article-meta">
      <div class="article-author">
        <?php if (!empty($article['profile_pic_path'])): ?>
          <img class="avatar-md" src="/task3/public/uploads/avatars/<?= htmlspecialchars($article['profile_pic_path']) ?>" alt="">
        <?php else: ?>
          <div class="avatar-md avatar-fallback"><?= strtoupper(substr($article['author_name'], 0, 1)) ?></div>
        <?php endif; ?>
        <div>
          <a href="/task3/public/author.php?id=<?= (int) $article['author_id'] ?>" class="author-name">
            <?= htmlspecialchars($article['author_name']) ?>
          </a>
          <p class="article-date"><?= date('F j, Y', strtotime($article['created_at'])) ?></p>
        </div>
      </div>
      <div class="article-stats">
        <span class="stat">
          <svg viewBox="0 0 16 16" fill="none"><path d="M8 3C4.5 3 1.5 8 1.5 8S4.5 13 8 13s6.5-5 6.5-5S11.5 3 8 3z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.2"/></svg>
          <?= (int) $article['view_count'] ?> views
        </span>
      </div>
    </div>

    <!-- Featured image -->
    <?php if (!empty($article['featured_image_path'])): ?>
      <div class="article-hero-img">
        <img src="/task3/public/uploads/articles/<?= htmlspecialchars($article['featured_image_path']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
      </div>
    <?php endif; ?>

    <!-- Tags -->
    <?php if (!empty($tags)): ?>
      <div class="tags-row">
        <?php foreach ($tags as $tag): ?>
          <span class="pill"><?= htmlspecialchars($tag) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Article body -->
    <div class="article-body">
      <?= nl2br(htmlspecialchars($article['body'])) ?>
    </div>

    <!-- Like button -->
    <div class="like-section">
      <button
        id="like-btn"
        class="like-btn <?= $userLiked ? 'liked' : '' ?>"
        data-id="<?= (int) $article['id'] ?>"
        aria-label="Like this article"
      >
        <svg viewBox="0 0 24 24" fill="<?= $userLiked ? 'currentColor' : 'none' ?>">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span id="like-count"><?= (int) $article['like_count'] ?></span>
        <span class="like-label"><?= $userLiked ? 'Liked' : 'Like' ?></span>
      </button>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <p class="like-hint"><a href="#">Login</a> to like this article</p>
      <?php endif; ?>
    </div>

    <!-- Comments section placeholder (Task 4 builds here) -->
    <div class="comments-section" id="comments-section">
      <h3 class="comments-heading">Comments</h3>
      <p style="color:#888;font-size:.9rem;">Comments are loaded by Task 4.</p>
    </div>

  </div>

</main>

<script src="/task3/public/js/main.js"></script>
</body>
</html>
