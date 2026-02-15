<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Loja.php';
require_once BASE_PATH . '/app/models/Produto.php';
require_once BASE_PATH . '/app/helpers/functions.php';

$id = (int)($_GET['id'] ?? 0);
$loja = $id ? Loja::find($id) : null;
$produtos = $loja ? Produto::byLoja($id) : [];

$page_title = $loja ? $loja['nome'] . ' - SabaraTem' : 'Loja - SabaraTem';
$page_description = $loja ? 'Produtos da loja ' . $loja['nome'] : 'Loja SabaraTem';
require_once BASE_PATH . '/includes/header.php';
?>
<?php if (!$loja): ?>
  <p>Loja nao encontrada.</p>
<?php else: ?>
  <section class="loja">
    <img class="logo" src="<?php echo e(img_src($loja['logo'])); ?>" alt="<?php echo e($loja['nome']); ?>">
    <div>
      <h1><?php echo e($loja['nome']); ?></h1>
      <?php if (!empty($loja['descricao'])): ?>
        <p class="muted"><?php echo e($loja['descricao']); ?></p>
      <?php endif; ?>
      <?php if (!empty($loja['endereco'])): ?>
        <p class="muted"><strong>Endereco:</strong> <?php echo e($loja['endereco']); ?></p>
      <?php endif; ?>
      <?php if (!empty($loja['telefone'])): ?>
        <p class="muted"><strong>Telefone:</strong> <?php echo e($loja['telefone']); ?></p>
      <?php endif; ?>
      <?php if (!empty($loja['horario'])): ?>
        <p class="muted"><strong>Horario:</strong> <?php echo e($loja['horario']); ?></p>
      <?php endif; ?>
      <a class="btn" href="<?php echo e(wa_link($loja['whatsapp'], 'Ola! Quero falar com a loja ' . $loja['nome'])); ?>" target="_blank">WhatsApp</a>
    </div>
  </section>

  <section class="grid">
    <?php foreach ($produtos as $p): ?>
      <article class="card">
        <img src="<?php echo e(img_src($p['imagem'])); ?>" alt="<?php echo e($p['nome']); ?>">
        <div class="card-body">
          <h2><?php echo e($p['nome']); ?></h2>
          <p class="price"><?php echo e(format_price($p['preco'])); ?></p>
          <a class="btn" href="produto.php?id=<?php echo e($p['id']); ?>">Ver produto</a>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
<?php endif; ?>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>
