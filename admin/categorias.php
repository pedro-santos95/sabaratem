<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Categoria.php';
require_once '../app/models/Subcategoria.php';
require_once '../app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST['form'] ?? 'categoria';

    if ($form === 'categoria') {
        $svg = $_POST['svg_atual'] ?? '';
        $upload = upload_image($_FILES['svg'] ?? null, 'categorias');
        if ($upload) {
            $svg = $upload;
        }

        $data = [
            'nome' => $_POST['nome'] ?? '',
            'svg' => $svg
        ];

        if (!empty($_POST['id'])) {
            Categoria::update((int)$_POST['id'], $data);
        } else {
            Categoria::create($data);
        }

        header('Location: categorias.php');
        exit;
    }

    if ($form === 'subcategoria') {
        $imagem = $_POST['imagem_atual'] ?? '';
        $upload = upload_image($_FILES['imagem'] ?? null, 'subcategorias');
        if ($upload) {
            $imagem = $upload;
        }

        $data = [
            'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
            'nome' => $_POST['nome'] ?? '',
            'imagem' => $imagem
        ];

        if (!empty($_POST['sub_id'])) {
            Subcategoria::update((int)$_POST['sub_id'], $data);
        } else {
            Subcategoria::create($data);
        }

        header('Location: categorias.php');
        exit;
    }
}

if (isset($_GET['delete'])) {
    Categoria::delete((int)$_GET['delete']);
    header('Location: categorias.php');
    exit;
}

if (isset($_GET['delete_subcategoria'])) {
    Subcategoria::delete((int)$_GET['delete_subcategoria']);
    header('Location: categorias.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = Categoria::find((int)$_GET['edit']);
}

$edit_sub = null;
if (isset($_GET['edit_subcategoria'])) {
    $edit_sub = Subcategoria::find((int)$_GET['edit_subcategoria']);
}

$categorias = Categoria::all();
$subcategorias = Subcategoria::all();
$page_title = 'Admin - Categorias';
$page_description = 'Cadastro de categorias e subcategorias';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <h1>Categorias</h1>

    <form class="form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="form" value="categoria">
      <input type="hidden" name="id" value="<?php echo e($edit['id'] ?? ''); ?>">
      <label>Nome
        <input type="text" name="nome" value="<?php echo e($edit['nome'] ?? ''); ?>" required>
      </label>
      <label>Imagem da categoria
        <input type="file" name="svg" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
        <input type="hidden" name="svg_atual" value="<?php echo e($edit['svg'] ?? ''); ?>">
        <?php if (!empty($edit['svg'])): ?>
          <small class="muted">Atual: <?php echo e($edit['svg']); ?></small>
        <?php endif; ?>
      </label>
      <button class="btn" type="submit">Salvar</button>
    </form>

    <div class="table-wrap">
      <table class="table admin-table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>SVG</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categorias as $c): ?>
            <tr>
              <td data-label="Nome"><?php echo e($c['nome']); ?></td>
              <td data-label="SVG"><?php echo e($c['svg']); ?></td>
              <td data-label="A&ccedil;&otilde;es" class="table-actions">
                <a class="action-link edit" href="categorias.php?edit=<?php echo e($c['id']); ?>">Editar</a>
                <a class="action-link delete" href="categorias.php?delete=<?php echo e($c['id']); ?>" onclick="return confirm('Excluir categoria?');">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h1>Subcategorias</h1>

    <form class="form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="form" value="subcategoria">
      <input type="hidden" name="sub_id" value="<?php echo e($edit_sub['id'] ?? ''); ?>">
      <label>Categoria
        <select name="categoria_id" required>
          <option value="">Selecione</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?php echo e($c['id']); ?>" <?php echo ($edit_sub && (int)$edit_sub['categoria_id'] === (int)$c['id']) ? 'selected' : ''; ?>><?php echo e($c['nome']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Nome da subcategoria
        <input type="text" name="nome" value="<?php echo e($edit_sub['nome'] ?? ''); ?>" required>
      </label>
      <label>Imagem da subcategoria
        <input type="file" name="imagem" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
        <input type="hidden" name="imagem_atual" value="<?php echo e($edit_sub['imagem'] ?? ''); ?>">
        <?php if (!empty($edit_sub['imagem'])): ?>
          <small class="muted">Atual: <?php echo e($edit_sub['imagem']); ?></small>
        <?php endif; ?>
      </label>
      <button class="btn" type="submit">Salvar</button>
    </form>

    <div class="table-wrap">
      <table class="table admin-table">
        <thead>
          <tr>
            <th>Categoria</th>
            <th>Subcategoria</th>
            <th>Imagem</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subcategorias as $s): ?>
            <tr>
              <td data-label="Categoria"><?php echo e($s['categoria_nome']); ?></td>
              <td data-label="Subcategoria"><?php echo e($s['nome']); ?></td>
              <td data-label="Imagem"><?php echo e($s['imagem']); ?></td>
              <td data-label="A&ccedil;&otilde;es" class="table-actions">
                <a class="action-link edit" href="categorias.php?edit_subcategoria=<?php echo e($s['id']); ?>">Editar</a>
                <a class="action-link delete" href="categorias.php?delete_subcategoria=<?php echo e($s['id']); ?>" onclick="return confirm('Excluir subcategoria?');">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>



