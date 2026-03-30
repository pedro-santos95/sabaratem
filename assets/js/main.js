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
  var redirectParam = encodeURIComponent(window.location.pathname + window.location.search);

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
          '<div class="search-empty">Clique na lupa para pesquisar pelo texto digitado.</div>';
        results.classList.add('active');
        return;
      }

      var html = '<div class="search-title">Resultados para "' + query.replace(/"/g, '') + '"</div>';
      items.forEach(function (item) {
        html += '<a class="search-item" href="' + publicBase + '/produto.php?id=' + item.id + '">' +
          item.nome +
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
          '<div class="card-actions">' +
            '<a class="btn" href="' + publicBase + '/produto.php?id=' + p.id + '">Ver produto</a>' +
            '<a class="btn-outline" href="' + publicBase + '/carrinho.php?action=add&produto_id=' + p.id + '&redirect=' + redirectParam + '">Adicionar ao carrinho</a>' +
            (whatsapp ? '<a class="btn-outline" target="_blank" rel="noopener" href="https://wa.me/' + whatsapp + '?text=' + encodeURIComponent('Olá! Tenho interesse no produto: ' + p.nome) + '">WhatsApp</a>' : '') +
          '</div>' +
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
      if (results) {
        results.innerHTML = '';
        results.classList.remove('active');
      }
    });
  }

  function setCookie(name, value, days) {
    var expires = '';
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + encodeURIComponent(value || '') + expires + '; path=/';
  }

  function getCookie(name) {
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) === ' ') {
        c = c.substring(1, c.length);
      }
      if (c.indexOf(nameEQ) === 0) {
        return decodeURIComponent(c.substring(nameEQ.length, c.length));
      }
    }
    return '';
  }

  function formatBRL(value) {
    var number = Number(value) || 0;
    return 'R$ ' + number.toFixed(2).replace('.', ',');
  }

  function readOrders() {
    var raw = getCookie('sbt_orders');
    if (!raw) {
      return [];
    }
    try {
      var parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  function writeOrders(orders) {
    if (!Array.isArray(orders)) {
      return;
    }
    setCookie('sbt_orders', JSON.stringify(orders), 365);
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/\"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  var cartForm = document.getElementById('cart-form');
  if (cartForm) {
    var cartTotalItems = document.getElementById('cart-total-items');
    var cartTotalPrice = document.getElementById('cart-total-price');
    var cartCheckoutButtons = document.querySelectorAll('.js-checkout-open');
    var cartSaveTimer = null;
    var lastSnapshot = { items: [], totalItems: 0, totalValue: 0 };
    var saleTracked = false;

    function buildCartMessage(rows, totalValue) {
      var storeName = cartForm.getAttribute('data-store-name') || '';
      var lines = [];
      if (storeName) {
        lines.push('Olá! Quero finalizar meu pedido na loja ' + storeName + ':');
      }
      rows.forEach(function (row) {
        var qtyInput = row.querySelector('.js-qty');
        var qty = qtyInput ? parseInt(qtyInput.value, 10) : 0;
        if (!qty || qty < 0) {
          return;
        }
        var unit = Number(row.dataset.unitPrice || 0);
        var subtotal = qty * unit;
        var name = row.dataset.name || '';
        lines.push(qty + 'x ' + name + ' - ' + formatBRL(unit) + ' (subtotal ' + formatBRL(subtotal) + ')');
      });
      lines.push('Total: ' + formatBRL(totalValue));
      return lines.join('\n');
    }

    function recalcCart() {
      var rows = Array.prototype.slice.call(cartForm.querySelectorAll('.cart-row'));
      var totalItems = 0;
      var totalValue = 0;
      var items = [];

      rows.forEach(function (row) {
        var qtyInput = row.querySelector('.js-qty');
        var qty = qtyInput ? parseInt(qtyInput.value, 10) : 0;
        if (isNaN(qty) || qty < 0) {
          qty = 0;
        }
        var unit = Number(row.dataset.unitPrice || 0);
        var subtotal = qty * unit;
        totalItems += qty;
        totalValue += subtotal;

        var subtotalEl = row.querySelector('.js-subtotal');
        if (subtotalEl) {
          subtotalEl.textContent = formatBRL(subtotal);
        }

        if (qty > 0) {
          items.push({
            name: row.dataset.name || '',
            qty: qty,
            unit: unit,
            subtotal: subtotal
          });
        }
      });

      if (cartTotalItems) {
        cartTotalItems.textContent = totalItems;
      }
      if (cartTotalPrice) {
        cartTotalPrice.textContent = formatBRL(totalValue);
      }

      if (cartCheckoutButtons.length) {
        var message = buildCartMessage(rows, totalValue);
        cartCheckoutButtons.forEach(function (btn) {
          btn.setAttribute('data-wa-message', message);
        });
      }

      lastSnapshot = {
        items: items,
        totalItems: totalItems,
        totalValue: totalValue
      };
    }

    function scheduleCartSave() {
      if (cartSaveTimer) {
        clearTimeout(cartSaveTimer);
      }
      cartSaveTimer = setTimeout(function () {
        var formData = new FormData(cartForm);
        formData.set('action', 'update');
        formData.set('ajax', '1');
        fetch(cartForm.getAttribute('action'), {
          method: 'POST',
          body: formData
        }).catch(function () {});
      }, 600);
    }

    cartForm.addEventListener('input', function (e) {
      if (e.target && e.target.classList.contains('js-qty')) {
        recalcCart();
      }
    });

    cartForm.addEventListener('change', function (e) {
      if (e.target && e.target.classList.contains('js-qty')) {
        recalcCart();
        scheduleCartSave();
      }
    });

    recalcCart();

    function storeOrder(customer) {
      if (!lastSnapshot.items.length) {
        return;
      }
      var storeName = cartForm.getAttribute('data-store-name') || '';
      var storeId = cartForm.getAttribute('data-store-id') || '';
      var orders = readOrders();
      var order = {
        id: Date.now(),
        date: new Date().toISOString(),
        store: storeName,
        store_id: storeId,
        total: lastSnapshot.totalValue,
        items: lastSnapshot.items,
        customer: customer
      };
      orders.unshift(order);
      if (orders.length > 10) {
        orders = orders.slice(0, 10);
      }
      writeOrders(orders);
    }

    function clearCartAfterOrder() {
      var lojaIdInput = cartForm.querySelector('input[name="loja_id"]');
      var lojaId = lojaIdInput ? lojaIdInput.value : '';
      if (!lojaId) {
        return;
      }
      var payload = new URLSearchParams();
      payload.set('action', 'clear');
      payload.set('loja_id', lojaId);
      payload.set('ajax', '1');
      if (navigator.sendBeacon) {
        navigator.sendBeacon(cartForm.getAttribute('action'), payload);
      } else {
        fetch(cartForm.getAttribute('action'), {
          method: 'POST',
          body: payload
        }).catch(function () {});
      }
    }

    function trackWhatsappSale() {
      if (saleTracked) {
        return;
      }
      if (!lastSnapshot.totalItems || lastSnapshot.totalItems < 1) {
        return;
      }
      saleTracked = true;
      var payload = new URLSearchParams();
      payload.set('count', String(lastSnapshot.totalItems));
      fetch(publicBase + '/track_order.php', {
        method: 'POST',
        body: payload
      }).catch(function () {});
    }

    cartForm.dataset.storeOrder = 'ready';
    cartForm.storeOrder = storeOrder;
    cartForm.clearCartAfterOrder = clearCartAfterOrder;
    cartForm.trackWhatsappSale = trackWhatsappSale;
  }

  var cartsModal = document.getElementById('carts-modal');
  var cartLinks = document.querySelectorAll('.js-open-carts');

  function bindModalClosers(modal, closeFn) {
    if (!modal) {
      return;
    }
    var closers = modal.querySelectorAll('[data-modal-close]');
    closers.forEach(function (el) {
      ['click', 'touchend'].forEach(function (evt) {
        el.addEventListener(evt, function (e) {
          e.preventDefault();
          closeFn();
        });
      });
    });
  }

  function openCartsModal() {
    if (!cartsModal) {
      return;
    }
    cartsModal.classList.add('active');
    cartsModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
  }

  function closeCartsModal() {
    if (!cartsModal) {
      return;
    }
    cartsModal.classList.remove('active');
    cartsModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
  }

  if (cartsModal) {
    bindModalClosers(cartsModal, closeCartsModal);

    cartLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        openCartsModal();
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && cartsModal.classList.contains('active')) {
        closeCartsModal();
      }
    });

    if (cartsModal.dataset.autoOpen === '1') {
      openCartsModal();
    }
  }

  var checkoutModal = document.getElementById('checkout-modal');
  var checkoutForm = checkoutModal ? checkoutModal.querySelector('#checkout-form') : null;
  var checkoutButtons = document.querySelectorAll('.js-checkout-open');

  function openCheckoutModal(button) {
    if (!checkoutModal || !checkoutForm) {
      return;
    }
    checkoutModal.dataset.waPhone = button.getAttribute('data-wa-phone') || '';
    checkoutModal.dataset.waMessage = button.getAttribute('data-wa-message') || '';

    var addressInput = checkoutForm.querySelector('[name="address"]');
    var contactInput = checkoutForm.querySelector('[name="contact"]');
    var paymentSelect = checkoutForm.querySelector('[name="payment"]');

    if (addressInput) {
      addressInput.value = getCookie('sbt_address');
    }
    if (contactInput) {
      contactInput.value = getCookie('sbt_contact');
    }
    if (paymentSelect) {
      paymentSelect.value = getCookie('sbt_payment');
    }

    checkoutModal.classList.add('active');
    checkoutModal.setAttribute('aria-hidden', 'false');
  }

  function closeCheckoutModal() {
    if (!checkoutModal) {
      return;
    }
    checkoutModal.classList.remove('active');
    checkoutModal.setAttribute('aria-hidden', 'true');
  }

  if (checkoutButtons.length && checkoutModal && checkoutForm) {
    bindModalClosers(checkoutModal, closeCheckoutModal);

    checkoutButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        openCheckoutModal(button);
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && checkoutModal.classList.contains('active')) {
        closeCheckoutModal();
      }
    });

    checkoutForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!checkoutForm.reportValidity()) {
        return;
      }

      var address = checkoutForm.querySelector('[name="address"]').value.trim();
      var contact = checkoutForm.querySelector('[name="contact"]').value.trim();
      var payment = checkoutForm.querySelector('[name="payment"]').value.trim();

      setCookie('sbt_address', address, 365);
      setCookie('sbt_contact', contact, 365);
      setCookie('sbt_payment', payment, 365);

      var customer = {
        address: address,
        contact: contact,
        payment: payment
      };
      if (cartForm && cartForm.storeOrder) {
        cartForm.storeOrder(customer);
        cartForm.trackWhatsappSale();
        cartForm.clearCartAfterOrder();
      }

      var baseMessage = checkoutModal.dataset.waMessage || '';
      var phone = checkoutModal.dataset.waPhone || '';
      if (!phone) {
        closeCheckoutModal();
        return;
      }

      var lines = [];
      if (baseMessage) {
        lines.push(baseMessage);
      }
      lines.push('');
      lines.push('Dados do cliente:');
      lines.push('Endereço: ' + address);
      lines.push('Contato: ' + contact);
      lines.push('Pagamento: ' + payment);

      var finalMessage = lines.join('\n');
      var link = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(finalMessage);
      window.open(link, '_blank');
      closeCheckoutModal();
      if (cartForm) {
        window.location.href = publicBase + '/meus-pedidos.php';
      }
    });
  }

  function renderOrdersPage() {
    var list = document.getElementById('orders-list');
    if (!list) {
      return;
    }
    var emptyState = document.getElementById('orders-empty');
    var orders = readOrders();
    if (!orders.length) {
      if (emptyState) {
        emptyState.style.display = 'block';
      }
      list.innerHTML = '';
      return;
    }
    if (emptyState) {
      emptyState.style.display = 'none';
    }

    var html = '';
    orders.forEach(function (order) {
      var storeName = escapeHtml(order.store || 'Pedido');
      var orderDate = '';
      if (order.date) {
        var parsedDate = new Date(order.date);
        if (!isNaN(parsedDate.getTime())) {
          orderDate = parsedDate.toLocaleString('pt-BR');
        }
      }
      var itemsHtml = '';
      if (Array.isArray(order.items)) {
        order.items.forEach(function (item) {
          var name = escapeHtml(item.name || '');
          var qty = Number(item.qty || 0);
          var unit = Number(item.unit || 0);
          var subtotal = Number(item.subtotal || (qty * unit));
          itemsHtml += '<div class="order-item">' + qty + 'x ' + name + ' - ' + formatBRL(unit) + ' (subtotal ' + formatBRL(subtotal) + ')</div>';
        });
      }

      var customer = order.customer || {};
      var address = escapeHtml(customer.address || '');
      var contact = escapeHtml(customer.contact || '');
      var payment = escapeHtml(customer.payment || '');
      var total = formatBRL(order.total || 0);
      var storeLink = order.store_id ? '<a class="btn-outline" href="' + publicBase + '/loja.php?id=' + encodeURIComponent(order.store_id) + '">Ver loja</a>' : '';

      html += '<article class="order-card">' +
        '<div class="order-header">' +
          '<div>' +
            '<h3>' + storeName + '</h3>' +
            (orderDate ? '<span class="order-date">' + escapeHtml(orderDate) + '</span>' : '') +
          '</div>' +
          storeLink +
        '</div>' +
        '<div class="order-meta">Pagamento: ' + payment + '</div>' +
        '<div class="order-meta">Contato: ' + contact + '</div>' +
        '<div class="order-meta">Endereço: ' + address + '</div>' +
        '<div class="order-items">' + itemsHtml + '</div>' +
        '<div class="order-total">Total: <strong>' + total + '</strong></div>' +
      '</article>';
    });

    list.innerHTML = html;
  }

  renderOrdersPage();
});




