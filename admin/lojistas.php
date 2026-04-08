<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Loja.php';
require_once '../app/models/Produto.php';
require_once '../app/helpers/functions.php';

$lojistas = Loja::all();
$loja_ids = array_map(function ($l) {
    return (int)$l['id'];
}, $lojistas);
$produto_counts = Produto::countByLojas($loja_ids);

$page_title = 'Admin - Lojistas';
$page_description = 'Gestão de lojistas';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <div class="admin-toolbar">
      <div>
        <h1>Lojistas</h1>
        <p class="muted">Gerencie lojistas e acesse os produtos vinculados.</p>
      </div>
      <a class="btn" href="lojista.php">Novo lojista</a>
    </div>

    <?php if (!$lojistas): ?>
      <div class="cart-empty">
        <p class="muted">Nenhum lojista cadastrado ainda.</p>
        <a class="btn" href="lojista.php">Cadastrar primeiro lojista</a>
      </div>
    <?php else: ?>
      <div class="store-grid">
        <?php foreach ($lojistas as $l): ?>
          <?php $count = $produto_counts[(int)$l['id']] ?? 0; ?>
          <article class="store-card">
            <div class="store-head">
              <img class="store-logo" src="<?php echo e(img_src($l['logo'] ?? '')); ?>" alt="<?php echo e($l['nome']); ?>" loading="lazy" decoding="async">
              <div>
                <h2><?php echo e($l['nome']); ?></h2>
                <div class="store-meta">
                  <?php if (!empty($l['whatsapp'])): ?>
                    <span>WhatsApp: <?php echo e($l['whatsapp']); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($l['telefone'])): ?>
                    <span>Telefone: <?php echo e($l['telefone']); ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="store-meta">
              <span>Produtos cadastrados: <?php echo e($count); ?></span>
              <?php if (!empty($l['endereco'])): ?>
                <span><?php echo e($l['endereco']); ?></span>
              <?php endif; ?>
            </div>
            <div class="store-card-actions">
              <a class="btn" href="lojista.php?id=<?php echo e($l['id']); ?>">Gerenciar</a>
              <a class="btn-outline" href="../loja.php?id=<?php echo e($l['id']); ?>" target="_blank">Ver vitrine</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>
