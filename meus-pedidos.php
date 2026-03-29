<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/helpers/functions.php';

$page_title = 'Meus pedidos - SabaraTem';
$page_description = 'HistÃ³rico de pedidos enviados';
require_once BASE_PATH . '/includes/header.php';
?>
<section class="section">
  <div class="section-title">
    <h2>Meus pedidos</h2>
  </div>
  <div id="orders-empty" class="cart-empty">
    <p class="muted">VocÃª ainda nÃ£o tem pedidos registrados.</p>
    <a class="btn" href="<?php echo e($public_base); ?>/index.php">Ver produtos</a>
  </div>
  <div id="orders-list" class="orders-list"></div>
</section>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>
