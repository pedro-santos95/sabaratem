<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Produto.php';
require_once BASE_PATH . '/app/models/Categoria.php';
require_once BASE_PATH . '/app/models/Subcategoria.php';
require_once BASE_PATH . '/app/helpers/functions.php';

$categoria_id = (int)($_GET['categoria_id'] ?? 0);
$subcategoria_id = (int)($_GET['subcategoria_id'] ?? 0);
$promo_param = $_GET['promo'] ?? null;
$promo = $promo_param !== null ? ((int)$promo_param === 1) : ($categoria_id === 0);
$q = trim($_GET['q'] ?? '');
$search_query = $q;

$categorias = Categoria::all();
$subcategorias = $categoria_id ? Subcategoria::byCategoria($categoria_id) : [];
$produtos = Produto::all($categoria_id ?: null, $q ?: null, $promo, $subcategoria_id ?: null);

$page_title = 'SabaráTem - Vitrine';
$page_description = 'Vitrine virtual local com compra via WhatsApp';
require_once BASE_PATH . '/includes/header.php';
?>
<section class="hero">
  <div class="hero-inner">
    <div class="hero-content">
      <span class="hero-chip">Descubra Sabará</span>
      <h1>Tudo o que você precisa, <span>Sabará tem</span>.</h1>
      <p>Explore o comércio local em um só lugar. Escolha seus produtos favoritos e compre direto pelo WhatsApp.</p>
      <div class="hero-actions">
        <a class="btn" href="#produtos">Ver produtos</a>
      </div>
    </div>
    <div class="hero-media">
      <img src="<?php echo e($asset_base); ?>/img/banner.png" alt="igreja" loading="eager" fetchpriority="high" decoding="async">
    </div>
  </div>
</section>

<section class="catalog">
  <div class="catalog-layout">
    <aside class="catalog-sidebar">
      <div class="sidebar-title">Categorias</div>
      <nav class="sidebar-list" data-filter="categories">
        <a href="<?php echo e($public_base); ?>/index.php?promo=1" data-promocao="1" data-categoria="" data-force-nav="1" class="sidebar-link <?php echo ($promo && empty($categoria_id)) ? 'active' : ''; ?>">Promoções</a>
        <?php foreach ($categorias as $c): ?>
          <a href="<?php echo e($public_base); ?>/index.php?categoria_id=<?php echo e($c['id']); ?>" data-categoria="<?php echo e($c['id']); ?>" data-force-nav="1" class="sidebar-link <?php echo ($categoria_id == $c['id']) ? 'active' : ''; ?>"><?php echo e($c['nome']); ?></a>
        <?php endforeach; ?>
      </nav>

      <?php if ($categoria_id && $subcategorias): ?>
        <div class="sidebar-title">Subcategorias</div>
        <nav class="sidebar-sublist" data-filter="subcategories" data-categoria="<?php echo e($categoria_id); ?>">
          <a href="<?php echo e($public_base); ?>/index.php?categoria_id=<?php echo e($categoria_id); ?>" data-subcategoria="" data-categoria="<?php echo e($categoria_id); ?>" data-force-nav="1" class="sidebar-link <?php echo empty($subcategoria_id) ? 'active' : ''; ?>">Todas</a>
          <?php foreach ($subcategorias as $s): ?>
            <a href="<?php echo e($public_base); ?>/index.php?categoria_id=<?php echo e($categoria_id); ?>&subcategoria_id=<?php echo e($s['id']); ?>" data-subcategoria="<?php echo e($s['id']); ?>" data-categoria="<?php echo e($categoria_id); ?>" data-force-nav="1" class="sidebar-link <?php echo ($subcategoria_id == $s['id']) ? 'active' : ''; ?>"><?php echo e($s['nome']); ?></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>
    </aside>

    <div class="catalog-content">
      <div class="mobile-filters">
        <div class="section-title">
          <h2>Categorias</h2>
        </div>
        <div class="cat-grid mobile-cat-grid mobile-chip-row" data-filter="categories">
          <a href="<?php echo e($public_base); ?>/index.php?promo=1" data-promocao="1" data-categoria="" data-force-nav="1" class="cat-item <?php echo ($promo && empty($categoria_id)) ? 'active' : ''; ?>">
            <span class="cat-icon">%</span>
            <span class="cat-name">Promoções</span>
          </a>
          <?php foreach ($categorias as $c): ?>
            <a href="<?php echo e($public_base); ?>/index.php?categoria_id=<?php echo e($c['id']); ?>" data-categoria="<?php echo e($c['id']); ?>" data-force-nav="1" class="cat-item <?php echo ($categoria_id == $c['id']) ? 'active' : ''; ?>">
              <span class="cat-icon">
                <?php if (!empty($c['svg'])): ?>
                  <img src="<?php echo e(asset_path($c['svg'])); ?>" alt="<?php echo e($c['nome']); ?>" loading="lazy" decoding="async">
                <?php else: ?>
                  <?php echo e(mb_substr($c['nome'], 0, 1)); ?>
                <?php endif; ?>
              </span>
              <span class="cat-name"><?php echo e($c['nome']); ?></span>
            </a>
          <?php endforeach; ?>
        </div>

        <?php if ($categoria_id && $subcategorias): ?>
          <div class="section-title">
            <h2>Subcategorias</h2>
          </div>
          <div class="cat-grid subcat-grid mobile-subcat-grid" data-filter="subcategories" data-categoria="<?php echo e($categoria_id); ?>">
            <a href="<?php echo e($public_base); ?>/index.php?categoria_id=<?php echo e($categoria_id); ?>" data-subcategoria="" data-categoria="<?php echo e($categoria_id); ?>" data-force-nav="1" class="cat-item <?php echo empty($subcategoria_id) ? 'active' : ''; ?>">
              <span class="cat-icon">#</span>
              <span class="cat-name">Todas</span>
            </a>
            <?php foreach ($subcategorias as $s): ?>
              <a href="<?php echo e($public_base); ?>/index.php?categoria_id=<?php echo e($categoria_id); ?>&subcategoria_id=<?php echo e($s['id']); ?>" data-subcategoria="<?php echo e($s['id']); ?>" data-categoria="<?php echo e($categoria_id); ?>" data-force-nav="1" class="cat-item <?php echo ($subcategoria_id == $s['id']) ? 'active' : ''; ?>">
                <span class="cat-icon">
  <?php if (!empty($s['imagem'])): ?>
    <img src="<?php echo e(asset_path($s['imagem'])); ?>" alt="<?php echo e($s['nome']); ?>" loading="lazy" decoding="async">
  <?php else: ?>
    <?php echo e(mb_substr($s['nome'], 0, 1)); ?>
  <?php endif; ?>
</span>
                <span class="cat-name"><?php echo e($s['nome']); ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <section class="section" id="produtos">
        <div class="catalog-header">
          <div>
            <h2>Explore a Vitrine</h2>
            <p class="muted">Mostrando <?php echo count($produtos); ?> itens encontrados</p>
          </div>
        </div>
        <div class="grid" id="products-grid">
    <?php foreach ($produtos as $p): ?>
      <article class="card">        <?php if (!empty($p['promo_ativa'])): ?>
          <span class="promo-badge">
            <?php if (($p['tipo_desconto'] ?? '') === 'percentual'): ?>
              <?php $percent = rtrim(rtrim(number_format((float)$p['valor_desconto'], 2, ',', '.'), '0'), ','); ?>
              -<?php echo e($percent); ?>%
            <?php else: ?>
              -<?php echo e(format_price($p['valor_desconto'])); ?>
            <?php endif; ?>
          </span>
        <?php endif; ?>
        <img src="<?php echo e(img_src($p['imagem'])); ?>" alt="<?php echo e($p['nome']); ?>" loading="lazy" decoding="async">
        <div class="card-body">
          <h2><?php echo e($p['nome']); ?></h2>
          <div class="price-block">
            <?php if (!empty($p['promo_ativa'])): ?>
              <span class="price-original"><?php echo e(format_price($p['preco'])); ?></span>
              <span class="price price-final"><?php echo e(format_price($p['preco_final'])); ?></span>
            <?php else: ?>
              <span class="price price-final"><?php echo e(format_price($p['preco'])); ?></span>
            <?php endif; ?>
            <?php if (!empty($p['preco_alternativo'])): ?>
              <span class="price-alt">Preço alternativo: <?php echo e(format_price($p['preco_alternativo'])); ?></span>
            <?php endif; ?>
            <?php if (!empty($p['texto_alternativo'])): ?>
              <span class="price-note"><?php echo e($p['texto_alternativo']); ?></span>
            <?php endif; ?>
            <?php if (!empty($p['promo_ativa']) && !empty($p['data_fim_promocao'])): ?>
              <?php
                $fim = DateTime::createFromFormat('Y-m-d', $p['data_fim_promocao']);
                $mostrar_prazo = false;
                if ($fim) {
                  $hoje = new DateTime('today');
                  if ($fim >= $hoje) {
                    $dias = (int)$hoje->diff($fim)->format('%a');
                    $mostrar_prazo = $dias <= 3;
                  }
                }
              ?>
              <?php if ($mostrar_prazo): ?>
                <span class="price-deadline">Válido até <?php echo e($fim->format('d/m/Y')); ?></span>
              <?php endif; ?>
            <?php endif; ?>
          </div>
          <div class="card-actions">
            <a class="btn" href="produto.php?id=<?php echo e($p['id']); ?>">Ver produto</a>
            <a class="btn-outline" href="<?php echo e(wa_link($p['loja_whatsapp'], 'Olá! Tenho interesse no produto: ' . $p['nome'])); ?>" target="_blank">WhatsApp</a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
    </div>
  </div>
</section>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>





















