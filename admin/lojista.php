<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Loja.php';
require_once '../app/models/Produto.php';
require_once '../app/helpers/functions.php';

$loja_id = (int)($_GET['id'] ?? 0);

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
        $loja_id = (int)$_POST['id'];
        Loja::update($loja_id, $data);
    } else {
        $loja_id = (int)Loja::create($data);
    }

    header('Location: lojista.php?id=' . $loja_id . '&saved=1');
    exit;
}

if ($loja_id > 0 && isset($_GET['delete'])) {
    Loja::delete($loja_id);
    header('Location: lojistas.php');
    exit;
}

if ($loja_id > 0 && isset($_GET['delete_prod'])) {
    $produto_id = (int)$_GET['delete_prod'];
    $produto = $produto_id ? Produto::find($produto_id) : null;
    if ($produto && (int)$produto['loja_id'] === $loja_id) {
        Produto::delete($produto_id);
    }
    header('Location: lojista.php?id=' . $loja_id . '#produtos');
    exit;
}

$loja = $loja_id > 0 ? Loja::find($loja_id) : null;
$produtos = $loja ? Produto::byLojaAdmin($loja_id) : [];
$saved = isset($_GET['saved']);

$page_title = $loja ? 'Admin - ' . $loja['nome'] : 'Admin - Novo lojista';
$page_description = 'Detalhes do lojista';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <div class="admin-toolbar">
      <div>
        <h1><?php echo e($loja ? $loja['nome'] : 'Novo lojista'); ?></h1>
        <p class="muted">Ajuste os dados do lojista e gerencie os produtos vinculados.</p>
      </div>
      <div class="admin-toolbar-actions">
        <a class="btn-outline" href="lojistas.php">Voltar</a>
        <?php if ($loja): ?>
          <a class="action-link delete" href="lojista.php?id=<?php echo e($loja_id); ?>&delete=1" onclick="return confirm('Excluir lojista e todos os produtos?');">Excluir lojista</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($saved): ?>
      <div class="form-success">Lojista salvo com sucesso.</div>
    <?php endif; ?>

    <div class="admin-tabs">
      <a href="#dados" class="active">Dados da loja</a>
      <a href="#produtos">Produtos da loja</a>
    </div>

    <section class="admin-section" id="dados">
      <h2>Dados da loja</h2>
      <form class="form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo e($loja['id'] ?? ''); ?>">
        <label>Nome
          <input type="text" name="nome" value="<?php echo e($loja['nome'] ?? ''); ?>" required>
        </label>
        <label>WhatsApp
          <input type="text" name="whatsapp" value="<?php echo e($loja['whatsapp'] ?? ''); ?>" required>
        </label>
        <label>Telefone
          <input type="text" name="telefone" value="<?php echo e($loja['telefone'] ?? ''); ?>">
        </label>
        <label>Endere&ccedil;o
          <input type="text" name="endereco" value="<?php echo e($loja['endereco'] ?? ''); ?>">
        </label>
        <label>Hor&aacute;rio
          <input type="text" name="horario" value="<?php echo e($loja['horario'] ?? ''); ?>">
        </label>
        <label>Descri&ccedil;&atilde;o
          <textarea name="descricao" rows="3"><?php echo e($loja['descricao'] ?? ''); ?></textarea>
        </label>
        <label>Logo
          <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
          <input type="hidden" name="logo_atual" value="<?php echo e($loja['logo'] ?? ''); ?>">
          <?php if (!empty($loja['logo'])): ?>
            <small class="muted">Atual: <?php echo e($loja['logo']); ?></small>
          <?php endif; ?>
        </label>
        <button class="btn" type="submit">Salvar</button>
      </form>
    </section>

    <section class="admin-section" id="produtos">
      <div class="admin-toolbar">
        <div>
          <h2>Produtos da loja</h2>
          <p class="muted">Edite ou remova produtos vinculados a este lojista.</p>
        </div>
        <?php if ($loja): ?>
          <a class="btn" href="produtos.php?loja_id=<?php echo e($loja_id); ?>">Novo produto</a>
        <?php endif; ?>
      </div>

      <?php if (!$loja): ?>
        <div class="cart-empty">
          <p class="muted">Salve o lojista para poder gerenciar produtos.</p>
        </div>
      <?php elseif (!$produtos): ?>
        <div class="cart-empty">
          <p class="muted">Nenhum produto cadastrado para esta loja.</p>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table admin-table">
            <thead>
              <tr>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Subcategoria</th>
                <th>Pre&ccedil;o</th>
                <th>A&ccedil;&otilde;es</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produtos as $p): ?>
                <tr>
                  <td data-label="Produto"><?php echo e($p['nome']); ?></td>
                  <td data-label="Categoria"><?php echo e($p['categoria_nome'] ?? ''); ?></td>
                  <td data-label="Subcategoria"><?php echo e($p['subcategoria_nome'] ?? ''); ?></td>
                  <td data-label="Pre&ccedil;o"><?php echo e(format_price($p['preco'])); ?></td>
                  <td data-label="A&ccedil;&otilde;es" class="table-actions">
                    <a class="action-link edit" href="produtos.php?edit=<?php echo e($p['id']); ?>">Editar</a>
                    <a class="action-link delete" href="lojista.php?id=<?php echo e($loja_id); ?>&delete_prod=<?php echo e($p['id']); ?>" onclick="return confirm('Excluir produto?');">Excluir</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>
