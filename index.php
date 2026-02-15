<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Produto.php';
require_once BASE_PATH . '/app/models/Categoria.php';
require_once BASE_PATH . '/app/helpers/functions.php';

$categoria_id = (int)($_GET['categoria_id'] ?? 0);
$promo_param = $_GET['promo'] ?? null;
$promo = $promo_param !== null ? ((int)$promo_param === 1) : ($categoria_id === 0);
$q = trim($_GET['q'] ?? '');
$search_query = $q;

$categorias = Categoria::all();
$produtos = Produto::all($categoria_id ?: null, $q ?: null, $promo);

$page_title = 'SabaraTem - Vitrine';
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
        <a class="btn" href="#produtos">Ver Produtos</a>
        <a class="btn-outline" href="<?php echo e($base); ?>/admin/index.php">Cadastrar Loja</a>
      </div>
    </div>
    <div class="hero-media">
      <img src="<?php echo e($asset_base); ?>/img/banner.png" alt="Sabará">
    </div>
  </div>
</section>

<section class="section" id="categorias">
  <div class="section-title">
    <h2>Categorias</h2>
  </div>
  <div class="cat-grid" data-filter="categories">
    <a href="<?php echo e($public_base); ?>/index.php?promo=1" data-promocao="1" data-categoria="" class="cat-item <?php echo ($promo && empty($categoria_id)) ? 'active' : ''; ?>">
      <span class="cat-icon">%</span>
      <span class="cat-name">Promocoes</span>
    </a>
    <?php foreach ($categorias as $c): ?>
      <a href="<?php echo e($public_base); ?>/index.php?categoria_id=<?php echo e($c['id']); ?>" data-categoria="<?php echo e($c['id']); ?>" class="cat-item <?php echo ($categoria_id == $c['id']) ? 'active' : ''; ?>">
        <span class="cat-icon">
          <?php if (!empty($c['svg'])): ?>
            <img src="<?php echo e(asset_path($c['svg'])); ?>" alt="<?php echo e($c['nome']); ?>">
          <?php else: ?>
            <?php echo e(mb_substr($c['nome'], 0, 1)); ?>
          <?php endif; ?>
        </span>
        <span class="cat-name"><?php echo e($c['nome']); ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="section" id="produtos">
  <div class="section-title">
    <h2>Produtos</h2>
  </div>
  <div class="grid" id="products-grid">
    <?php foreach ($produtos as $p): ?>
      <article class="card">
        <?php if (!empty($p['em_promocao']) && (int)$p['porcentagem_promocao'] > 0): ?>
          <span class="promo-badge">-<?php echo e((int)$p['porcentagem_promocao']); ?>%</span>
        <?php endif; ?>
        <img src="<?php echo e(img_src($p['imagem'])); ?>" alt="<?php echo e($p['nome']); ?>">
        <div class="card-body">
          <h2><?php echo e($p['nome']); ?></h2>
          <p class="price"><?php echo e(format_price($p['preco_final'] ?? $p['preco'])); ?></p>
          <a class="btn" href="produto.php?id=<?php echo e($p['id']); ?>">Ver produto</a>
          <a class="btn-outline" href="<?php echo e(wa_link($p['loja_whatsapp'], 'Ola! Tenho interesse no produto: ' . $p['nome'])); ?>" target="_blank">WhatsApp</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>
