<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Loja.php';
require_once '../app/models/Produto.php';

$page_title = 'Admin - Dashboard';
$page_description = 'Painel administrativo SabaraTem';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <h1>Dashboard</h1>
    <div class="metrics">
      <div class="metric-card">
        <span>Total de lojas</span>
        <strong><?php echo Loja::count(); ?></strong>
      </div>
      <div class="metric-card">
        <span>Total de produtos</span>
        <strong><?php echo Produto::count(); ?></strong>
      </div>
    </div>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>
