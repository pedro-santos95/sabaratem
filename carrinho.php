<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Produto.php';
require_once BASE_PATH . '/app/models/Loja.php';
require_once BASE_PATH . '/app/helpers/functions.php';
require_once BASE_PATH . '/app/helpers/cart.php';

start_session();

$action = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $remove_id = (int)($_POST['remove_id'] ?? 0);
    if ($remove_id > 0) {
        $action = 'remove';
    } else {
        $action = $_POST['action'] ?? 'update';
    }
} else {
    $action = $_GET['action'] ?? '';
}

if ($action !== '') {
    $redirect = cart_sanitize_redirect($_POST['redirect'] ?? $_GET['redirect'] ?? '');

    if ($action === 'add') {
        $produto_id = (int)($_POST['produto_id'] ?? $_GET['produto_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? $_GET['qty'] ?? 1);
        $produto = $produto_id ? Produto::find($produto_id) : null;
        if ($produto) {
            cart_add_item($produto['loja_id'], $produto['id'], $qty);
            $loja_id = (int)$produto['loja_id'];
            if ($redirect !== '') {
                header('Location: ' . $redirect);
                exit;
            }
            header('Location: carrinho.php?loja_id=' . $loja_id);
            exit;
        }
        header('Location: index.php');
        exit;
    }

    if ($action === 'remove') {
        $loja_id = (int)($_POST['loja_id'] ?? 0);
        $produto_id = (int)($_POST['remove_id'] ?? $_POST['produto_id'] ?? 0);
        if ($loja_id > 0 && $produto_id > 0) {
            $produto = Produto::find($produto_id);
            if ($produto && (int)$produto['loja_id'] === $loja_id) {
                cart_remove_item($loja_id, $produto_id);
            }
        }
        if ($redirect !== '') {
            header('Location: ' . $redirect);
            exit;
        }
        header('Location: carrinho.php?loja_id=' . $loja_id);
        exit;
    }

    if ($action === 'clear') {
        $loja_id = (int)($_POST['loja_id'] ?? 0);
        if ($loja_id > 0) {
            cart_clear_loja($loja_id);
        }
        $is_ajax = (($_POST['ajax'] ?? '') === '1');
        if ($is_ajax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true]);
            exit;
        }
        if ($redirect !== '') {
            header('Location: ' . $redirect);
            exit;
        }
        header('Location: carrinho.php?loja_id=' . $loja_id);
        exit;
    }

    if ($action === 'update') {
        $loja_id = (int)($_POST['loja_id'] ?? 0);
        $quantidades = $_POST['qty'] ?? [];
        if ($loja_id > 0 && is_array($quantidades)) {
            foreach ($quantidades as $produto_id => $qty) {
                $produto_id = (int)$produto_id;
                $produto = $produto_id ? Produto::find($produto_id) : null;
                if (!$produto || (int)$produto['loja_id'] !== $loja_id) {
                    continue;
                }
                cart_set_item($loja_id, $produto_id, (int)$qty);
            }
        }
        $is_ajax = (($_POST['ajax'] ?? '') === '1');
        if ($is_ajax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true]);
            exit;
        }
        if ($redirect !== '') {
            header('Location: ' . $redirect);
            exit;
        }
        header('Location: carrinho.php?loja_id=' . $loja_id);
        exit;
    }
}

$loja_id = (int)($_GET['loja_id'] ?? 0);
$loja = $loja_id ? Loja::find($loja_id) : null;
$carts = cart_get();
$items = ($loja_id > 0 && isset($carts[$loja_id]) && is_array($carts[$loja_id])) ? $carts[$loja_id] : [];

$cart_rows = [];
$total = 0;
$total_items = 0;

if ($loja && $items) {
    foreach ($items as $produto_id => $qty) {
        $produto = Produto::find($produto_id);
        if (!$produto || (int)$produto['loja_id'] !== $loja_id) {
            cart_remove_item($loja_id, $produto_id);
            continue;
        }
        $qty = (int)$qty;
        if ($qty <= 0) {
            continue;
        }
        $preco = !empty($produto['promo_ativa']) ? (float)$produto['preco_final'] : (float)$produto['preco'];
        $subtotal = $preco * $qty;
        $total += $subtotal;
        $total_items += $qty;
        $cart_rows[] = [
            'id' => (int)$produto['id'],
            'nome' => $produto['nome'],
            'imagem' => $produto['imagem'],
            'preco' => $preco,
            'preco_original' => (float)$produto['preco'],
            'promo_ativa' => !empty($produto['promo_ativa']),
            'qty' => $qty,
            'subtotal' => $subtotal,
        ];
    }
}

$page_title = $loja ? 'Carrinho - ' . $loja['nome'] : 'Carrinho';
$page_description = $loja ? 'Carrinho da loja ' . $loja['nome'] : 'Carrinho de compras';
require_once BASE_PATH . '/includes/header.php';

$current_path = $_SERVER['REQUEST_URI'] ?? '/carrinho.php';

$whatsapp_link = '';
$wa_phone = '';
$base_message = '';
if ($loja && $cart_rows) {
    $linhas = [];
    $linhas[] = 'Olá! Quero finalizar meu pedido na loja ' . $loja['nome'] . ':';
    foreach ($cart_rows as $item) {
        $linha = $item['qty'] . 'x ' . $item['nome'] . ' - ' . format_price($item['preco']) . ' (subtotal ' . format_price($item['subtotal']) . ')';
        $linhas[] = $linha;
    }
    $linhas[] = 'Total: ' . format_price($total);
    $base_message = implode("\n", $linhas);
    $whatsapp_link = wa_link($loja['whatsapp'], $base_message);

    $wa_phone = preg_replace('/\D+/', '', (string)($loja['whatsapp'] ?? ''));
    if (strlen($wa_phone) <= 11 && $wa_phone !== '') {
        $wa_phone = '55' . $wa_phone;
    }
}
?>
<section class="section">
  <?php if (!$loja): ?>
    <p class="muted">Carrinho n&atilde;o encontrado.</p>
    <a class="btn" href="<?php echo e($public_base); ?>/index.php">Ver produtos</a>
  <?php elseif (!$cart_rows): ?>
    <div class="section-title">
      <h2>Carrinho - <?php echo e($loja['nome']); ?></h2>
    </div>
    <p class="muted">Seu carrinho est&aacute; vazio.</p>
    <a class="btn" href="<?php echo e($public_base); ?>/index.php">Ver produtos</a>
  <?php else: ?>
    <div class="section-title cart-header">
      <h2>Carrinho - <?php echo e($loja['nome']); ?></h2>
      <a class="btn-outline" href="<?php echo e($public_base); ?>/index.php">Continuar comprando</a>
    </div>
    <form id="cart-form" method="post" action="<?php echo e($public_base); ?>/carrinho.php" data-store-name="<?php echo e($loja['nome']); ?>" data-store-id="<?php echo e($loja_id); ?>">
      <input type="hidden" name="loja_id" value="<?php echo e($loja_id); ?>">
      <input type="hidden" name="redirect" value="<?php echo e($current_path); ?>">
      <div class="table-wrap">
        <table class="table cart-table">
          <thead>
            <tr>
              <th>Produto</th>
              <th>PreÃ§o</th>
              <th>Qtd.</th>
              <th>Subtotal</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart_rows as $item): ?>
              <tr class="cart-row" data-id="<?php echo e($item['id']); ?>" data-unit-price="<?php echo e(number_format($item['preco'], 2, '.', '')); ?>" data-name="<?php echo e($item['nome']); ?>">
                <td data-label="Produto">
                  <div class="cart-item">
                    <img src="<?php echo e(img_src($item['imagem'])); ?>" alt="<?php echo e($item['nome']); ?>" loading="lazy" decoding="async">
                    <div>
                      <strong><?php echo e($item['nome']); ?></strong>
                      <?php if ($item['promo_ativa']): ?>
                        <div class="muted">De <?php echo e(format_price($item['preco_original'])); ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td data-label="Preço"><?php echo e(format_price($item['preco'])); ?></td>
                <td data-label="Qtd.">
                  <input class="qty-input js-qty" type="number" name="qty[<?php echo e($item['id']); ?>]" min="0" value="<?php echo e($item['qty']); ?>">
                </td>
                <td data-label="Subtotal"><span class="js-subtotal"><?php echo e(format_price($item['subtotal'])); ?></span></td>
                <td data-label="Ação">
                  <button class="action-link delete" type="submit" name="remove_id" value="<?php echo e($item['id']); ?>">Remover</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="cart-summary">
        <div class="summary-card">
          <span>Itens</span>
          <strong id="cart-total-items"><?php echo e($total_items); ?></strong>
        </div>
        <div class="summary-card">
          <span>Total</span>
          <strong id="cart-total-price"><?php echo e(format_price($total)); ?></strong>
        </div>
        <div class="summary-actions">
          <button class="btn-outline" type="submit" name="action" value="update">Atualizar carrinho</button>
          <?php if ($whatsapp_link): ?>
            <button
              class="btn js-checkout-open"
              type="button"
              data-wa-phone="<?php echo e($wa_phone); ?>"
              data-wa-message="<?php echo e($base_message); ?>"
            >Finalizar no WhatsApp</button>
            <noscript>
              <a class="btn" href="<?php echo e($whatsapp_link); ?>" target="_blank">Finalizar no WhatsApp</a>
            </noscript>
          <?php endif; ?>
        </div>
      </div>
    </form>
    <form class="cart-clear" method="post" action="<?php echo e($public_base); ?>/carrinho.php">
      <input type="hidden" name="action" value="clear">
      <input type="hidden" name="loja_id" value="<?php echo e($loja_id); ?>">
      <input type="hidden" name="redirect" value="<?php echo e($current_path); ?>">
      <button class="btn-outline" type="submit">Limpar carrinho</button>
    </form>
  <?php endif; ?>
</section>
<?php if ($whatsapp_link): ?>
  <div class="modal" id="checkout-modal" aria-hidden="true">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="checkout-title">
      <button class="modal-close" type="button" data-modal-close aria-label="Fechar">×</button>
      <h3 id="checkout-title">Dados para finalizar</h3>
      <form id="checkout-form">
        <label>
          Endereço
          <textarea name="address" rows="3" required></textarea>
        </label>
        <label>
          Contato
          <input type="text" name="contact" required>
        </label>
        <label>
          Forma de pagamento
          <select name="payment" required>
            <option value="">Selecione</option>
            <option value="PIX">PIX</option>
            <option value="Dinheiro">Dinheiro</option>
            <option value="Cartão de crédito">Cartão de crédito</option>
            <option value="Cartão de débito">Cartão de débito</option>
            <option value="Outro">Outro</option>
          </select>
        </label>
        <div class="modal-actions">
          <button class="btn-outline" type="button" data-modal-close>Cancelar</button>
          <button class="btn" type="submit">Enviar no WhatsApp</button>
        </div>
      </form>
      <p class="muted modal-hint">Seus dados ficam salvos neste navegador para facilitar as próximas compras.</p>
    </div>
  </div>
<?php endif; ?>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>
