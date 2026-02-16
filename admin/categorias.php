<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Categoria.php';
require_once '../app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome' => $_POST['nome'] ?? '',
        'svg' => $_POST['svg'] ?? ''
    ];

    if (!empty($_POST['id'])) {
        Categoria::update((int)$_POST['id'], $data);
    } else {
        Categoria::create($data);
    }

    header('Location: categorias.php');
    exit;
}

if (isset($_GET['delete'])) {
    Categoria::delete((int)$_GET['delete']);
    header('Location: categorias.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = Categoria::find((int)$_GET['edit']);
}

$categorias = Categoria::all();
$page_title = 'Admin - Categorias';
$page_description = 'Cadastro de categorias';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <h1>Categorias</h1>

    <form class="form" method="post">
      <input type="hidden" name="id" value="<?php echo e($edit['id'] ?? ''); ?>">
      <label>Nome
        <input type="text" name="nome" value="<?php echo e($edit['nome'] ?? ''); ?>" required>
      </label>
      <label>SVG (caminho ou URL)
        <input type="text" name="svg" value="<?php echo e($edit['svg'] ?? ''); ?>" placeholder="assets/img/categorias/bebidas.svg">
      </label>
      <button class="btn" type="submit">Salvar</button>
    </form>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>SVG</th>
            <th>Acoes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categorias as $c): ?>
            <tr>
              <td><?php echo e($c['nome']); ?></td>
              <td><?php echo e($c['svg']); ?></td>
              <td class="table-actions">
                <a class="action-link edit" href="categorias.php?edit=<?php echo e($c['id']); ?>">Editar</a>
                <a class="action-link delete" href="categorias.php?delete=<?php echo e($c['id']); ?>" onclick="return confirm('Excluir categoria?');">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>
