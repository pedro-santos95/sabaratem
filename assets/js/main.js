document.addEventListener('DOMContentLoaded', function () {
  var links = document.querySelectorAll('a');
  links.forEach(function (link) {
    if (link.getAttribute('target') === '_blank') {
      link.setAttribute('rel', 'noopener');
    }
  });

  var publicBaseAttr = document.body.getAttribute('data-public-base');
  var publicBase = publicBaseAttr !== null ? publicBaseAttr : '';
  var assetBaseAttr = document.body.getAttribute('data-asset-base');
  var assetBase = assetBaseAttr || (publicBase + '/assets');

  function normalizeAsset(path) {
    if (!path) {
      return assetBase + '/img/placeholder.svg';
    }
    if (/^https?:\/\//i.test(path)) {
      return path;
    }
    if (path.indexOf('../') === 0 || path.indexOf('./') === 0) {
      return path;
    }
    if (path.indexOf('assets/') === 0) {
      return assetBase + '/' + path.replace(/^assets\//, '');
    }
    return path;
  }

  var input = document.getElementById('search-input');
  var results = document.getElementById('search-results');

  if (input && results) {
    var controller = null;
    var lastQuery = '';

    function clearResults() {
      results.innerHTML = '';
      results.classList.remove('active');
    }

    function renderItems(payload) {
      var query = payload && payload.query ? payload.query : '';
      var items = payload && payload.items ? payload.items : [];
      if (!items.length) {
        results.innerHTML = '<div class="search-title">Nenhum resultado para "' + query.replace(/"/g, '') + '"</div>' +
          '<div class="search-empty">Tente buscar por nome do produto, loja ou categoria.</div>';
        results.classList.add('active');
        return;
      }

      var html = '<div class="search-title">Resultados para "' + query.replace(/"/g, '') + '"</div>';
      items.forEach(function (item) {
        var img = normalizeAsset(item.imagem);
        var meta = [];
        if (item.loja_nome) {
          meta.push('Loja: ' + item.loja_nome);
        }
        if (item.categoria_nome) {
          meta.push('Categoria: ' + item.categoria_nome);
        }
        html += '<a class="search-item" href="' + publicBase + '/produto.php?id=' + item.id + '">' +
          '<img src="' + img + '" alt="' + item.nome.replace(/"/g, '') + '" loading="lazy" decoding="async">' +
          '<div><strong>' + item.nome + '</strong>' +
          (meta.length ? '<small>' + meta.join(' · ') + '</small>' : '') +
          '</div>' +
        '</a>';
      });
      results.innerHTML = html;
      results.classList.add('active');
    }

    function fetchResults(q) {
      if (controller) {
        controller.abort();
      }
      controller = new AbortController();

      fetch(publicBase + '/search.php?q=' + encodeURIComponent(q), { signal: controller.signal })
        .then(function (res) {
          if (!res.ok) {
            throw new Error('Falha na busca');
          }
          return res.json();
        })
        .then(function (data) { renderItems(data); })
        .catch(function () {
          clearResults();
        });
    }

    input.addEventListener('input', function () {
      var q = input.value.trim();
      if (q.length < 2) {
        clearResults();
        return;
      }
      if (q === lastQuery) {
        return;
      }
      lastQuery = q;
      fetchResults(q);
    });

    document.addEventListener('click', function (e) {
      if (!results.contains(e.target) && e.target !== input) {
        clearResults();
      }
    });
  }

  var menuToggle = document.querySelector('.menu-toggle');
  var mobileMenu = document.getElementById('mobile-menu');
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function () {
      var isOpen = mobileMenu.classList.toggle('open');
      menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  var categories = document.querySelector('[data-filter="categories"]');
  var grid = document.getElementById('products-grid');
  var searchForm = document.querySelector('.search form');

  function renderProducts(items) {
    var html = '';
    items.forEach(function (p) {
      var img = normalizeAsset(p.imagem);
      var whatsapp = (p.loja_whatsapp || '').replace(/\D+/g, '');
      var badge = '';
      if (Number(p.em_promocao) === 1 && Number(p.porcentagem_promocao) > 0) {
        badge = '<span class="promo-badge">-' + Number(p.porcentagem_promocao) + '%</span>';
      }
      html += '<article class="card">' +
        badge +
        '<img src="' + img + '" alt="' + p.nome.replace(/"/g, '') + '" loading="lazy" decoding="async">' +
        '<div class="card-body">' +
          '<h2>' + p.nome + '</h2>' +
          '<p class="price">R$ ' + Number(p.preco_final || p.preco).toFixed(2).replace('.', ',') + '</p>' +
          '<a class="btn" href="' + publicBase + '/produto.php?id=' + p.id + '">Ver produto</a>' +
          (whatsapp ? '<a class="btn-outline" target="_blank" rel="noopener" href="https://wa.me/' + whatsapp + '?text=' + encodeURIComponent('Ola! Tenho interesse no produto: ' + p.nome) + '">WhatsApp</a>' : '') +
        '</div>' +
      '</article>';
    });
    grid.innerHTML = html || '<p class="muted">Nenhum produto encontrado.</p>';
  }

  function fetchProducts(params) {
    var url = publicBase + '/products.php';
    if (params) {
      url += '?' + params;
    }
    return fetch(url)
      .then(function (res) {
        if (!res.ok) {
          throw new Error('Falha ao carregar produtos');
        }
        return res.json();
      })
      .then(function (items) { renderProducts(items); });
  }

  if (categories && grid) {
    categories.addEventListener('click', function (e) {
      var link = e.target.closest('[data-categoria]');
      if (!link) {
        return;
      }
      e.preventDefault();

      var categoriaId = link.getAttribute('data-categoria') || '';
      var promo = link.getAttribute('data-promocao') === '1';
      var q = input ? input.value.trim() : '';
      var params = [];
      if (promo) {
        params.push('promo=1');
      } else if (categoriaId) {
        params.push('categoria_id=' + encodeURIComponent(categoriaId));
      }
      if (q) {
        params.push('q=' + encodeURIComponent(q));
      }

      fetchProducts(params.join('&')).catch(function () {
        window.location.href = link.getAttribute('href');
      });

      var itemsEls = categories.querySelectorAll('.cat-item');
      itemsEls.forEach(function (el) { el.classList.remove('active'); });
      link.classList.add('active');
    });
  }

  if (searchForm && grid) {
    searchForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var q = input ? input.value.trim() : '';
      var active = categories ? categories.querySelector('.cat-item.active') : null;
      var categoriaId = active ? active.getAttribute('data-categoria') : '';
      var promo = active ? active.getAttribute('data-promocao') === '1' : false;
      var params = [];
      if (promo) {
        params.push('promo=1');
      } else if (categoriaId) {
        params.push('categoria_id=' + encodeURIComponent(categoriaId));
      }
      if (q) {
        params.push('q=' + encodeURIComponent(q));
      }
      fetchProducts(params.join('&'));
    });
  }
});
