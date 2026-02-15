<?php
require_once '../app/helpers/functions.php';
require_once '../app/config/database.php';
require_once '../app/config/admin.php';

start_session();

if (is_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = $_POST['pass'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ?');
    $stmt->execute([$user]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($pass . $ADMIN_PEPPER, $admin['password_hash'])) {
        admin_login($admin['id'], $admin['username']);
        header('Location: index.php');
        exit;
    } else {
        $error = 'Credenciais invalidas.';
    }
}

$page_title = 'Admin - Login';
$page_description = 'Acesso restrito ao admin';
require_once '../includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-header">
      <span class="auth-badge">Admin</span>
      <h1>Login</h1>
      <p>Entre com suas credenciais para acessar o painel.</p>
    </div>

    <form class="form" method="post">
      <?php if ($error): ?>
        <p class="form-error"><?php echo e($error); ?></p>
      <?php endif; ?>
      <label>Usuario
        <input type="text" name="user" required>
      </label>
      <label>Senha
        <input type="password" name="pass" required>
      </label>
      <button type="submit">Entrar</button>
    </form>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
