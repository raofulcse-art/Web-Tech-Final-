<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BlogPress — Home</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/task3/public/css/style.css">
</head>
<body>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="main">

  <!-- Hero section -->
  <section class="hero">
    <p class="hero-eyebrow">Latest Stories</p>
    <h1 class="hero-title">Ideas worth reading.</h1>
  </section>

  <!-- Category filter tabs -->
  <div class="tabs-bar">
    <div class="tabs" role="tablist">
      <button class="tab active" data-id="" role="tab" aria-selected="true">All</button>
      <?php foreach ($categories as $cat): ?>
        <button
          class="tab"
          data-id="<?= (int) $cat['id'] ?>"
          role="tab"
          aria-selected="false"
        ><?= htmlspecialchars($cat['name']) ?></button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Article grid -->
  <div class="article-grid" id="article-grid">
    <?php if (empty($articles)): ?>
      <p class="empty-state">No articles published yet.</p>
    <?php else: ?>
      <?php foreach ($articles as $a): ?>
        <article class="card">
          <a href="/task3/controllers/ArticleController.php?id=<?= (int) $a['id'] ?>" class="card-image-link">
            <?php if (!empty($a['featured_image_path'])): ?>
              <img
                class="card-img"
                src="/task3/public/uploads/articles/<?= htmlspecialchars($a['featured_image_path']) ?>"
                alt="<?= htmlspecialchars($a['title']) ?>"
                loading="lazy"
              >
            <?php else: ?>
              <div class="card-img card-img-placeholder">
                <span><?= htmlspecialchars(substr($a['title'], 0, 2)) ?></span>
              </div>
            <?php endif; ?>
          </a>

          <div class="card-body">
            <?php if (!empty($a['category_name'])): ?>
              <span class="badge"><?= htmlspecialchars($a['category_name']) ?></span>
            <?php endif; ?>

            <h2 class="card-title">
              <a href="/task3/controllers/ArticleController.php?id=<?= (int) $a['id'] ?>">
                <?= htmlspecialchars($a['title']) ?>
              </a>
            </h2>

            <div class="card-meta">
              <div class="card-author">
                <?php if (!empty($a['profile_pic_path'])): ?>
                  <img class="avatar-sm" src="/task3/public/uploads/avatars/<?= htmlspecialchars($a['profile_pic_path']) ?>" alt="">
                <?php else: ?>
                  <div class="avatar-sm avatar-fallback"><?= strtoupper(substr($a['author_name'], 0, 1)) ?></div>
                <?php endif; ?>
                <span><?= htmlspecialchars($a['author_name']) ?></span>
              </div>
              <div class="card-stats">
                <span class="stat">
                  <svg viewBox="0 0 16 16" fill="none"><path d="M8 3C4.5 3 1.5 8 1.5 8S4.5 13 8 13s6.5-5 6.5-5S11.5 3 8 3z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.2"/></svg>
                  <?= (int) $a['view_count'] ?>
                </span>
                <span class="stat">
                  <svg viewBox="0 0 16 16" fill="none"><path d="M8 13.5l-1.1-1C3.4 9.4 1 7.2 1 4.7 1 2.9 2.4 1.5 4.2 1.5c1 0 2 .5 2.8 1.3.7-.8 1.7-1.3 2.8-1.3C11.6 1.5 13 2.9 13 4.7c0 2.5-2.4 4.7-5.9 7.8L8 13.5z" stroke="currentColor" stroke-width="1.2"/></svg>
                  <?= (int) $a['like_count'] ?>
                </span>
                <span class="stat date"><?= date('M j, Y', strtotime($a['created_at'])) ?></span>
              </div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</main>

<script src="/task3/public/js/main.js"></script>
</body>
</html>
