<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Loja.php';
require_once BASE_PATH . '/app/helpers/functions.php';

$lojistas = Loja::all();
$page_title = 'SabaraTem - Lojistas';
$page_description = 'Lista de lojistas locais';
require_once BASE_PATH . '/includes/header.php';
?>
<section class="section">
  <div class="section-title">
    <h2>Lojistas</h2>
  </div>
  <div class="grid">
    <?php foreach ($lojistas as $l): ?>
      <article class="card">
        <img src="<?php echo e(img_src($l['logo'] ?? '')); ?>" alt="<?php echo e($l['nome']); ?>" loading="lazy" decoding="async">
        <div class="card-body">
          <h2><?php echo e($l['nome']); ?></h2>
          <?php if (!empty($l['descricao'])): ?>
            <p class="muted"><?php echo e($l['descricao']); ?></p>
          <?php endif; ?>
          <?php if (!empty($l['endereco'])): ?>
            <p class="muted"><strong>Endereco:</strong> <?php echo e($l['endereco']); ?></p>
          <?php endif; ?>
          <?php if (!empty($l['telefone'])): ?>
            <p class="muted"><strong>Telefone:</strong> <?php echo e($l['telefone']); ?></p>
          <?php endif; ?>
          <?php if (!empty($l['horario'])): ?>
            <p class="muted"><strong>Horario:</strong> <?php echo e($l['horario']); ?></p>
          <?php endif; ?>
          <a class="btn" href="loja.php?id=<?php echo e($l['id']); ?>">Ver loja</a>
          <a class="btn-outline" href="<?php echo e(wa_link($l['whatsapp'], 'Ola! Quero falar com a loja ' . $l['nome'])); ?>" target="_blank">WhatsApp</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>
