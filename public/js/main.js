// ─────────────────────────────────────────────────────────────────────────
//  BlogPress — Task 3 main.js
//  Features: category filter tabs, live search, like/unlike
// ─────────────────────────────────────────────────────────────────────────

// ── Helper: build an article card HTML from a JS object ──────────────────
function buildCard(a) {
  const img = a.featured_image_path
    ? `<img class="card-img" src="/task3/public/uploads/articles/${a.featured_image_path}" alt="${escHtml(a.title)}" loading="lazy">`
    : `<div class="card-img card-img-placeholder"><span>${escHtml(a.title.substring(0,2))}</span></div>`;

  const avatar = a.profile_pic_path
    ? `<img class="avatar-sm" src="/task3/public/uploads/avatars/${a.profile_pic_path}" alt="">`
    : `<div class="avatar-sm avatar-fallback">${escHtml(a.author_name.charAt(0).toUpperCase())}</div>`;

  const category = a.category_name
    ? `<span class="badge">${escHtml(a.category_name)}</span>`
    : '';

  const date = new Date(a.created_at).toLocaleDateString('en-US', {
    month: 'short', day: 'numeric', year: 'numeric'
  });

  return `
    <article class="card">
      <a href="/task3/controllers/ArticleController.php?id=${a.id}" class="card-image-link">
        ${img}
      </a>
      <div class="card-body">
        ${category}
        <h2 class="card-title">
          <a href="/task3/controllers/ArticleController.php?id=${a.id}">${escHtml(a.title)}</a>
        </h2>
        <div class="card-meta">
          <div class="card-author">
            ${avatar}
            <span>${escHtml(a.author_name)}</span>
          </div>
          <div class="card-stats">
            <span class="stat">
              <svg viewBox="0 0 16 16" fill="none"><path d="M8 13.5l-1.1-1C3.4 9.4 1 7.2 1 4.7 1 2.9 2.4 1.5 4.2 1.5c1 0 2 .5 2.8 1.3.7-.8 1.7-1.3 2.8-1.3C11.6 1.5 13 2.9 13 4.7c0 2.5-2.4 4.7-5.9 7.8L8 13.5z" stroke="currentColor" stroke-width="1.2"/></svg>
              ${a.like_count || 0}
            </span>
            <span class="stat date">${date}</span>
          </div>
        </div>
      </div>
    </article>
  `;
}

// ── Helper: escape HTML to prevent XSS ───────────────────────────────────
function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = String(str ?? '');
  return d.innerHTML;
}


// ══════════════════════════════════════════════════════════════════════════
//  FEATURE 1 — CATEGORY FILTER TABS
// ══════════════════════════════════════════════════════════════════════════
const tabs = document.querySelectorAll('.tab');
const grid = document.getElementById('article-grid');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {

    // Update active state on tabs
    tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
    tab.classList.add('active');
    tab.setAttribute('aria-selected', 'true');

    const categoryId = tab.dataset.id;
    const url = categoryId
      ? `/task3/public/api/articles.php?category_id=${categoryId}`
      : `/task3/public/api/articles.php`;

    // Show loading state
    grid.innerHTML = '<div class="loading-spinner">Loading...</div>';

    fetch(url)
      .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      })
      .then(articles => {
        if (articles.length === 0) {
          grid.innerHTML = '<p class="empty-state">No articles in this category yet.</p>';
        } else {
          grid.innerHTML = articles.map(buildCard).join('');
        }
      })
      .catch(err => {
        console.error('Category filter error:', err);
        grid.innerHTML = '<p class="empty-state">Failed to load articles. Please try again.</p>';
      });

  });
});


// ══════════════════════════════════════════════════════════════════════════
//  FEATURE 2 — LIVE SEARCH (debounced 300ms)
// ══════════════════════════════════════════════════════════════════════════
const searchInput    = document.getElementById('search-input');
const searchDropdown = document.getElementById('search-dropdown');
let   searchTimer    = null;

if (searchInput) {

  searchInput.addEventListener('input', () => {
    // Cancel the previous pending search
    clearTimeout(searchTimer);

    const q = searchInput.value.trim();

    if (q.length < 2) {
      searchDropdown.innerHTML = '';
      searchDropdown.classList.add('hidden');
      return;
    }

    // Wait 300ms after user stops typing, THEN send the request
    searchTimer = setTimeout(() => {
      fetch(`/task3/public/api/search.php?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(results => {
          if (results.length === 0) {
            searchDropdown.innerHTML = '<div class="search-no-results">No results found</div>';
          } else {
            searchDropdown.innerHTML = results.map(r => {
              const thumb = r.featured_image_path
                ? `<img class="search-result-img" src="/task3/public/uploads/articles/${r.featured_image_path}" alt="">`
                : `<div class="search-result-img-placeholder">No img</div>`;
              return `
                <a href="/task3/controllers/ArticleController.php?id=${r.id}" class="search-result">
                  ${thumb}
                  <div class="search-result-info">
                    <strong>${escHtml(r.title)}</strong>
                    <small>${escHtml(r.author_name)}</small>
                  </div>
                </a>
              `;
            }).join('');
          }
          searchDropdown.classList.remove('hidden');
        })
        .catch(err => {
          console.error('Search error:', err);
        });
    }, 300); // ← 300ms debounce delay
  });

  // Close dropdown when user clicks anywhere else on the page
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-wrapper')) {
      searchDropdown.classList.add('hidden');
    }
  });

  // Close dropdown on Escape key
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      searchDropdown.classList.add('hidden');
      searchInput.blur();
    }
  });
}


// ══════════════════════════════════════════════════════════════════════════
//  FEATURE 3 — LIKE / UNLIKE
// ══════════════════════════════════════════════════════════════════════════
const likeBtn = document.getElementById('like-btn');

if (likeBtn) {
  likeBtn.addEventListener('click', () => {
    const articleId = parseInt(likeBtn.dataset.id, 10);

    // Optimistically disable button during request (prevent double-clicks)
    likeBtn.disabled = true;

    fetch('/task3/public/api/likes.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ article_id: articleId })
    })
    .then(res => {
      if (res.status === 401) {
        // Not logged in
        alert('Please log in to like articles!');
        return null;
      }
      if (!res.ok) throw new Error('Server error');
      return res.json();
    })
    .then(data => {
      if (!data) return;

      // Update count display
      document.getElementById('like-count').textContent = data.count;

      // Update label
      const label = likeBtn.querySelector('.like-label');
      if (label) label.textContent = data.liked ? 'Liked' : 'Like';

      // Toggle filled/outline heart SVG
      const heartPath = likeBtn.querySelector('svg path');
      if (heartPath) {
        heartPath.parentElement.setAttribute('fill', data.liked ? 'currentColor' : 'none');
      }

      // Toggle liked CSS class
      likeBtn.classList.toggle('liked', data.liked);
    })
    .catch(err => {
      console.error('Like error:', err);
      alert('Something went wrong. Please try again.');
    })
    .finally(() => {
      likeBtn.disabled = false;
    });
  });
}
