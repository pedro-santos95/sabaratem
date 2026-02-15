<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/config/admin.php';
require_once '../app/helpers/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($new === '' || $new !== $confirm) {
        $error = 'A nova senha e a confirmacao nao conferem.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash FROM admin_users WHERE id = ?');
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($current . $ADMIN_PEPPER, $admin['password_hash'])) {
            $error = 'Senha atual invalida.';
        } else {
            $new_hash = password_hash($new . $ADMIN_PEPPER, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?');
            $upd->execute([$new_hash, $admin['id']]);
            $success = 'Senha atualizada com sucesso.';
        }
    }
}

$page_title = 'Admin - Trocar Senha';
$page_description = 'Atualizacao de senha';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <h1>Trocar senha</h1>

    <form class="form" method="post">
      <?php if ($error): ?>
        <p><?php echo e($error); ?></p>
      <?php endif; ?>
      <?php if ($success): ?>
        <p><?php echo e($success); ?></p>
      <?php endif; ?>
      <label>Senha atual
        <input type="password" name="current" required>
      </label>
      <label>Nova senha
        <input type="password" name="new" required>
      </label>
      <label>Confirmar nova senha
        <input type="password" name="confirm" required>
      </label>
      <button type="submit">Salvar</button>
    </form>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>
