<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Loja.php';
require_once '../app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logo = $_POST['logo_atual'] ?? '';
    $upload = upload_image($_FILES['logo'] ?? null, 'lojistas');
    if ($upload) {
        $logo = $upload;
    }

    $data = [
        'nome' => $_POST['nome'] ?? '',
        'whatsapp' => $_POST['whatsapp'] ?? '',
        'logo' => $logo,
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

    <form class="form" method="post" enctype="multipart/form-data">
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
      <label>Endereço
        <input type="text" name="endereco" value="<?php echo e($edit['endereco'] ?? ''); ?>">
      </label>
      <label>Horário
        <input type="text" name="horario" value="<?php echo e($edit['horario'] ?? ''); ?>">
      </label>
      <label>Descrição
        <textarea name="descricao" rows="3"><?php echo e($edit['descricao'] ?? ''); ?></textarea>
      </label>
      <label>Logo
        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
        <input type="hidden" name="logo_atual" value="<?php echo e($edit['logo'] ?? ''); ?>">
        <?php if (!empty($edit['logo'])): ?>
          <small class="muted">Atual: <?php echo e($edit['logo']); ?></small>
        <?php endif; ?>
      </label>
      <button class="btn" type="submit">Salvar</button>
    </form>

    <div class="table-wrap">
      <table class="table admin-table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>WhatsApp</th>
            <th>Telefone</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lojistas as $l): ?>
            <tr>
              <td data-label="Nome"><?php echo e($l['nome']); ?></td>
              <td data-label="WhatsApp"><?php echo e($l['whatsapp']); ?></td>
              <td data-label="Telefone"><?php echo e($l['telefone']); ?></td>
              <td data-label="A&ccedil;&otilde;es" class="table-actions">
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

