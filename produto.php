<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Produto.php';
require_once BASE_PATH . '/app/helpers/functions.php';
require_once BASE_PATH . '/app/helpers/cart.php';

$id = (int)($_GET['id'] ?? 0);
$produto = $id ? Produto::find($id) : null;

$page_title = $produto ? $produto['nome'] . ' - SabaraTem' : 'Produto - SabaraTem';
$page_description = $produto ? $produto['descricao'] : 'Produto SabaraTem';
require_once BASE_PATH . '/includes/header.php';
?>
<?php if (!$produto): ?>
  <p>Produto não encontrado.</p>
<?php else: ?>
  <section class="produto">
    <img src="<?php echo e(img_src($produto['imagem'])); ?>" alt="<?php echo e($produto['nome']); ?>" loading="eager" fetchpriority="high" decoding="async">
    <div>
      <h1><?php echo e($produto['nome']); ?></h1>
      <div class="price-block">
        <?php if (!empty($produto['promo_ativa'])): ?>
          <span class="price-original"><?php echo e(format_price($produto['preco'])); ?></span>
          <span class="price price-final"><?php echo e(format_price($produto['preco_final'])); ?></span>
        <?php else: ?>
          <span class="price price-final"><?php echo e(format_price($produto['preco'])); ?></span>
        <?php endif; ?>
        <?php if (!empty($produto['preco_alternativo'])): ?>
          <span class="price-alt">Preço alternativo: <?php echo e(format_price($produto['preco_alternativo'])); ?></span>
        <?php endif; ?>
        <?php if (!empty($produto['texto_alternativo'])): ?>
          <span class="price-note"><?php echo e($produto['texto_alternativo']); ?></span>
        <?php endif; ?>
        <?php if (!empty($produto['promo_ativa']) && !empty($produto['data_fim_promocao'])): ?>
          <?php
            $fim = DateTime::createFromFormat('Y-m-d', $produto['data_fim_promocao']);
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
      <p><?php echo e($produto['descricao']); ?></p>
      <a class="btn" href="<?php echo e($public_base); ?>/carrinho.php?action=add&produto_id=<?php echo e($produto['id']); ?>&redirect=<?php echo e(rawurlencode($_SERVER['REQUEST_URI'] ?? '/produto.php?id=' . $produto['id'])); ?>">Adicionar ao carrinho</a>
      <a class="btn" href="<?php echo e(wa_link($produto['loja_whatsapp'], 'Olá! Tenho interesse no produto: ' . $produto['nome'])); ?>" target="_blank">Falar no WhatsApp</a>
      <a class="btn-outline" href="loja.php?id=<?php echo e($produto['loja_id']); ?>">Ver loja</a>
    </div>
  </section>
<?php endif; ?>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>





