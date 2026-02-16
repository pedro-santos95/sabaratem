<?php $admin_current = basename($_SERVER['PHP_SELF'] ?? ''); ?>
<aside class="sidebar">
  <a class="<?php echo $admin_current === 'index.php' ? 'active' : ''; ?>" href="index.php">Dashboard</a>
  <a class="<?php echo $admin_current === 'lojistas.php' ? 'active' : ''; ?>" href="lojistas.php">Lojistas</a>
  <a class="<?php echo $admin_current === 'produtos.php' ? 'active' : ''; ?>" href="produtos.php">Produtos</a>
  <a class="<?php echo $admin_current === 'categorias.php' ? 'active' : ''; ?>" href="categorias.php">Categorias</a>
  <a class="<?php echo $admin_current === 'senha.php' ? 'active' : ''; ?>" href="senha.php">Trocar senha</a>
  <a class="danger" href="logout.php">Sair</a>
</aside>
