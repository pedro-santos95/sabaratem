<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Loja.php';
require_once '../app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome' => $_POST['nome'] ?? '',
        'whatsapp' => $_POST['whatsapp'] ?? '',
        'logo' => $_POST['logo'] ?? '',
        'endereco' => $_POST['endereco'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'horario' => $_POST['horario'] ?? ''
    ];

    if (!empty($_POST['id'])) {
        Loja::update((int)$_POST['id'], $data);
    } else {
        Loja::create($data);
    }

    header('Location: lojistas.php');
    exit;
}

if (isset($_GET['delete'])) {
    Loja::delete((int)$_GET['delete']);
    header('Location: lojistas.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = Loja::find((int)$_GET['edit']);
}

$lojistas = Loja::all();
$page_title = 'Admin - Lojistas';
$page_description = 'Cadastro de lojistas';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <h1>Lojistas</h1>

    <form class="form" method="post">
      <input type="hidden" name="id" value="<?php echo e($edit['id'] ?? ''); ?>">
      <label>Nome
        <input type="text" name="nome" value="<?php echo e($edit['nome'] ?? ''); ?>" required>
      </label>
      <label>WhatsApp
        <input type="text" name="whatsapp" value="<?php echo e($edit['whatsapp'] ?? ''); ?>" required>
      </label>
      <label>Telefone
        <input type="text" name="telefone" value="<?php echo e($edit['telefone'] ?? ''); ?>">
      </label>
      <label>Endereco
        <input type="text" name="endereco" value="<?php echo e($edit['endereco'] ?? ''); ?>">
      </label>
      <label>Horario
        <input type="text" name="horario" value="<?php echo e($edit['horario'] ?? ''); ?>">
      </label>
      <label>Descricao
        <textarea name="descricao" rows="3"><?php echo e($edit['descricao'] ?? ''); ?></textarea>
      </label>
      <label>Logo (URL)
        <input type="text" name="logo" value="<?php echo e($edit['logo'] ?? ''); ?>">
      </label>
      <button class="btn" type="submit">Salvar</button>
    </form>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>WhatsApp</th>
            <th>Telefone</th>
            <th>Acoes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lojistas as $l): ?>
            <tr>
              <td><?php echo e($l['nome']); ?></td>
              <td><?php echo e($l['whatsapp']); ?></td>
              <td><?php echo e($l['telefone']); ?></td>
              <td class="table-actions">
                <a class="action-link edit" href="lojistas.php?edit=<?php echo e($l['id']); ?>">Editar</a>
                <a class="action-link delete" href="lojistas.php?delete=<?php echo e($l['id']); ?>" onclick="return confirm('Excluir lojista?');">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>
