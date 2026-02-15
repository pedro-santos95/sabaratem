<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Produto.php';
require_once BASE_PATH . '/app/helpers/functions.php';

$id = (int)($_GET['id'] ?? 0);
$produto = $id ? Produto::find($id) : null;

$page_title = $produto ? $produto['nome'] . ' - SabaraTem' : 'Produto - SabaraTem';
$page_description = $produto ? $produto['descricao'] : 'Produto SabaraTem';
require_once BASE_PATH . '/includes/header.php';
?>
<?php if (!$produto): ?>
  <p>Produto nao encontrado.</p>
<?php else: ?>
  <section class="produto">
    <img src="<?php echo e(img_src($produto['imagem'])); ?>" alt="<?php echo e($produto['nome']); ?>">
    <div>
      <h1><?php echo e($produto['nome']); ?></h1>
      <p class="price"><?php echo e(format_price($produto['preco_final'] ?? $produto['preco'])); ?></p>
      <p><?php echo e($produto['descricao']); ?></p>
      <a class="btn" href="<?php echo e(wa_link($produto['loja_whatsapp'], 'Ola! Tenho interesse no produto: ' . $produto['nome'])); ?>" target="_blank">Falar no WhatsApp</a>
      <a class="btn-outline" href="loja.php?id=<?php echo e($produto['loja_id']); ?>">Ver loja</a>
    </div>
  </section>
<?php endif; ?>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>
