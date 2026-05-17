<header class="site-header">
  <div class="header-inner">
    <a href="/task3/controllers/HomeController.php" class="logo">
      <span class="logo-dot"></span>
      BlogPress
    </a>

    <div class="search-wrapper">
      <svg class="search-icon" viewBox="0 0 20 20" fill="none">
        <circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/>
        <path d="M13 13l3.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
      <input
        type="text"
        id="search-input"
        placeholder="Search articles, tags..."
        autocomplete="off"
        aria-label="Search articles"
      />
      <div id="search-dropdown" class="search-dropdown hidden" role="listbox"></div>
    </div>

    <nav class="header-nav">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="nav-user">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
        <a href="#" class="nav-link">Logout</a>
      <?php else: ?>
        <a href="#" class="nav-link">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
