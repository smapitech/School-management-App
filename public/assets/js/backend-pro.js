(() => {
  const body = document.body;
  const sidebar = document.querySelector('.sidebar');
  const topbar = document.querySelector('.topbar');
  if (!sidebar || !topbar) return;

  const toggle = document.createElement('button');
  toggle.type = 'button';
  toggle.className = 'icon-button pro-mobile-toggle';
  toggle.setAttribute('aria-label', 'Open navigation');
  toggle.setAttribute('aria-expanded', 'false');
  toggle.innerHTML = '<span aria-hidden="true">&#9776;</span>';
  topbar.prepend(toggle);

  const backdrop = document.createElement('button');
  backdrop.type = 'button';
  backdrop.className = 'pro-sidebar-backdrop';
  backdrop.setAttribute('aria-label', 'Close navigation');
  backdrop.setAttribute('aria-hidden', 'true');
  body.appendChild(backdrop);

  const setSidebar = (open) => {
    body.classList.toggle('sidebar-open', open);
    toggle.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
  };

  toggle.addEventListener('click', () => setSidebar(!body.classList.contains('sidebar-open')));
  backdrop.addEventListener('click', () => setSidebar(false));
  sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setSidebar(false)));

  const mobileQuery = window.matchMedia('(max-width: 1100px)');
  const closeOnDesktop = (event) => {
    if (!event.matches) {
      setSidebar(false);
    }
  };
  if (typeof mobileQuery.addEventListener === 'function') {
    mobileQuery.addEventListener('change', closeOnDesktop);
  } else if (typeof mobileQuery.addListener === 'function') {
    mobileQuery.addListener(closeOnDesktop);
  }

  const searchButton = document.querySelector('.icon-button[title="Search"]');
  if (searchButton) {
    const links = [...sidebar.querySelectorAll('a[href]')].map((a) => ({
      title: (a.textContent || '').trim(),
      href: a.getAttribute('href') || '#'
    })).filter((item) => item.title && item.href !== '#');

    const modal = document.createElement('div');
    modal.className = 'pro-global-search';
    modal.innerHTML = `
      <div class="pro-search-panel" role="dialog" aria-modal="true" aria-label="Search modules">
        <input type="search" placeholder="Search modules and pages..." aria-label="Search modules and pages">
        <div class="pro-search-results"></div>
      </div>`;
    body.appendChild(modal);
    const input = modal.querySelector('input');
    const results = modal.querySelector('.pro-search-results');

    const render = (query = '') => {
      const q = query.trim().toLowerCase();
      const matches = links.filter((item) => !q || item.title.toLowerCase().includes(q)).slice(0, 18);
      results.innerHTML = matches.length
        ? matches.map((item) => `<a href="${item.href}">${item.title}</a>`).join('')
        : '<p class="pro-search-empty">No matching page found.</p>';
    };

    const openSearch = () => {
      modal.classList.add('is-open');
      render('');
      setTimeout(() => input.focus(), 30);
    };

    const closeSearch = () => modal.classList.remove('is-open');

    searchButton.addEventListener('click', openSearch);
    input.addEventListener('input', () => render(input.value));
    modal.addEventListener('click', (event) => { if (event.target === modal) closeSearch(); });
    document.addEventListener('keydown', (event) => {
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        openSearch();
      }
      if (event.key === 'Escape') {
        closeSearch();
        setSidebar(false);
      }
    });
  }

  const chartPalette = ['#247563', '#c66b4e', '#6d8f82', '#d7a85b', '#496f86', '#8b6d9b'];

  const parseChartData = (canvas) => {
    try {
      return JSON.parse(canvas.dataset.values || '{}');
    } catch (_) {
      return {};
    }
  };

  const fitCanvas = (canvas) => {
    const ratio = Math.max(1, window.devicePixelRatio || 1);
    const rect = canvas.getBoundingClientRect();
    const width = Math.max(280, Math.floor(rect.width));
    const height = Math.max(220, Math.floor(rect.height));
    canvas.width = width * ratio;
    canvas.height = height * ratio;
    const ctx = canvas.getContext('2d');
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    return { ctx, width, height };
  };

  const drawDoughnut = (canvas, data) => {
    const { ctx, width, height } = fitCanvas(canvas);
    const values = (data.values || []).map(Number);
    const total = Math.max(1, values.reduce((a, b) => a + b, 0));
    const radius = Math.min(width, height) * 0.31;
    const lineWidth = Math.max(22, radius * 0.28);
    const cx = width / 2;
    const cy = height / 2;
    let angle = -Math.PI / 2;
    ctx.clearRect(0, 0, width, height);
    values.forEach((value, index) => {
      const segment = (value / total) * Math.PI * 2;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, angle, angle + segment);
      ctx.strokeStyle = chartPalette[index % chartPalette.length];
      ctx.lineWidth = lineWidth;
      ctx.lineCap = 'round';
      ctx.stroke();
      angle += segment;
    });
    ctx.textAlign = 'center';
    ctx.fillStyle = '#1f302a';
    ctx.font = '700 27px system-ui, sans-serif';
    ctx.fillText(data.centerLabel || String(total), cx, cy - 2);
    ctx.fillStyle = '#708079';
    ctx.font = '500 12px system-ui, sans-serif';
    ctx.fillText(data.centerText || 'Total', cx, cy + 22);
  };

  const drawBar = (canvas, data) => {
    const { ctx, width, height } = fitCanvas(canvas);
    const labels = data.labels || [];
    const values = (data.values || []).map(Number);
    const max = Math.max(1, ...values);
    const left = 48;
    const right = 18;
    const top = 20;
    const bottom = 48;
    const chartW = width - left - right;
    const chartH = height - top - bottom;
    ctx.clearRect(0, 0, width, height);
    ctx.strokeStyle = '#e3ebe7';
    ctx.fillStyle = '#718079';
    ctx.font = '11px system-ui, sans-serif';
    ctx.textAlign = 'right';
    for (let i = 0; i <= 4; i++) {
      const y = top + (chartH * i / 4);
      const value = max - (max * i / 4);
      ctx.beginPath();
      ctx.moveTo(left, y);
      ctx.lineTo(width - right, y);
      ctx.stroke();
      ctx.fillText(data.prefix ? data.prefix + compactNumber(value) : compactNumber(value), left - 8, y + 4);
    }
    const slot = chartW / Math.max(1, values.length);
    const barW = Math.min(58, slot * 0.52);
    values.forEach((value, i) => {
      const h = (value / max) * chartH;
      const x = left + slot * i + (slot - barW) / 2;
      const y = top + chartH - h;
      ctx.fillStyle = chartPalette[i % chartPalette.length];
      roundRect(ctx, x, y, barW, h, 7);
      ctx.fill();
      ctx.fillStyle = '#56645e';
      ctx.textAlign = 'center';
      ctx.font = '11px system-ui, sans-serif';
      const label = String(labels[i] || '').length > 15 ? String(labels[i]).slice(0, 13) + '...' : String(labels[i] || '');
      ctx.fillText(label, x + barW / 2, height - 20);
    });
  };

  const compactNumber = (value) => {
    if (Math.abs(value) >= 1000000) return (value / 1000000).toFixed(1).replace('.0', '') + 'm';
    if (Math.abs(value) >= 1000) return (value / 1000).toFixed(1).replace('.0', '') + 'k';
    return Math.round(value).toString();
  };

  const roundRect = (ctx, x, y, w, h, r) => {
    const radius = Math.min(r, Math.abs(w) / 2, Math.abs(h) / 2);
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.arcTo(x + w, y, x + w, y + h, radius);
    ctx.arcTo(x + w, y + h, x, y + h, radius);
    ctx.arcTo(x, y + h, x, y, radius);
    ctx.arcTo(x, y, x + w, y, radius);
    ctx.closePath();
  };

  const renderCharts = () => {
    document.querySelectorAll('canvas.pro-chart').forEach((canvas) => {
      const data = parseChartData(canvas);
      if (canvas.dataset.chart === 'doughnut') drawDoughnut(canvas, data);
      if (canvas.dataset.chart === 'bar') drawBar(canvas, data);
    });
  };

  renderCharts();

  const permissionManager = document.querySelector('[data-permission-manager]');
  if (permissionManager) {
    const searchInput = permissionManager.querySelector('[data-permission-search]');
    const categoryFilter = permissionManager.querySelector('[data-permission-category-filter]');
    const rows = [...permissionManager.querySelectorAll('[data-permission-row]')];
    const sections = [...permissionManager.querySelectorAll('[data-permission-category-block]')];
    const bulkButtons = [...permissionManager.querySelectorAll('[data-permission-bulk]')];

    const normalize = (value) => String(value || '').trim().toLowerCase();

    const visibleRows = () => rows.filter((row) => !row.hidden);

    const applyFilters = () => {
      const query = normalize(searchInput ? searchInput.value : '');
      const category = normalize(categoryFilter ? categoryFilter.value : '');

      rows.forEach((row) => {
        const haystack = normalize([
          row.dataset.moduleName,
          row.dataset.moduleKey,
          row.dataset.moduleRoute,
          row.dataset.moduleCategory,
          row.textContent
        ].join(' '));
        const matchesQuery = !query || haystack.includes(query);
        const matchesCategory = !category || normalize(row.dataset.moduleCategory) === category;
        row.hidden = !(matchesQuery && matchesCategory);
      });

      sections.forEach((section) => {
        const sectionRows = [...section.querySelectorAll('[data-permission-row]')];
        section.hidden = sectionRows.every((row) => row.hidden);
      });
    };

    const setVisibleCheckboxes = (callback) => {
      visibleRows().forEach((row) => {
        row.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
          if (checkbox.disabled) {
            return;
          }
          callback(checkbox);
        });
      });
    };

    bulkButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const action = button.dataset.permissionBulk;
        if (action === 'view-all') {
          setVisibleCheckboxes((checkbox) => { checkbox.checked = true; });
        }
        if (action === 'clear-all') {
          setVisibleCheckboxes((checkbox) => { checkbox.checked = false; });
        }
        if (action === 'view-only') {
          setVisibleCheckboxes((checkbox) => {
            checkbox.checked = checkbox.dataset.permissionAction === 'view';
          });
        }
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', applyFilters);
    }

    if (categoryFilter) {
      categoryFilter.addEventListener('change', applyFilters);
    }

    applyFilters();
  }

  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(renderCharts, 120);
  });
})();
