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
    <div class="produto-info">
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
      <div class="produto-desc">
        <p><?php echo e($produto['descricao']); ?></p>
      </div>
      <div class="produto-actions">
        <a class="btn btn-cart" href="<?php echo e($public_base); ?>/carrinho.php?action=add&produto_id=<?php echo e($produto['id']); ?>&redirect=<?php echo e(rawurlencode($_SERVER['REQUEST_URI'] ?? '/produto.php?id=' . $produto['id'])); ?>">
          <span class="btn-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" role="presentation" focusable="false" aria-hidden="true">
              <circle cx="9" cy="20" r="1.5"></circle>
              <circle cx="17" cy="20" r="1.5"></circle>
              <path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L22 8H6"></path>
            </svg>
          </span>
          <span class="btn-text">Adicionar ao carrinho</span>
        </a>
        <a class="btn btn-whatsapp" href="<?php echo e(wa_link($produto['loja_whatsapp'], 'Olá! Tenho interesse no produto: ' . $produto['nome'])); ?>" target="_blank">
          <span class="btn-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" role="presentation" focusable="false" aria-hidden="true">
              <path d="M20 11a8 8 0 1 1-3-6.5A8 8 0 0 1 20 11Z"></path>
              <path d="M8.5 8.5c.8 1.5 1.8 2.5 3.3 3.3"></path>
              <path d="M11.8 11.8l1.6-1.6"></path>
              <path d="M4 20l1.5-4"></path>
            </svg>
          </span>
          <span class="btn-text">WhatsApp</span>
        </a>
        <a class="btn-outline btn-store" href="loja.php?id=<?php echo e($produto['loja_id']); ?>">
          <span class="btn-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" role="presentation" focusable="false" aria-hidden="true">
              <path d="M4 10h16"></path>
              <path d="M6 10V20h12V10"></path>
              <path d="M3 10l2-6h14l2 6"></path>
              <path d="M9 20v-6h6v6"></path>
            </svg>
          </span>
          <span class="btn-text">Ver loja</span>
        </a>
      </div>
    </div>
  </section>
<?php endif; ?>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>





