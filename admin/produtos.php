<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Produto.php';
require_once '../app/models/Loja.php';
require_once '../app/models/Categoria.php';
require_once '../app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $em_promocao = isset($_POST['em_promocao']) ? 1 : 0;
    $porcentagem_promocao = (int)($_POST['porcentagem_promocao'] ?? 0);
    $porcentagem_promocao = max(0, min(99, $porcentagem_promocao));

    $data = [
        'loja_id' => (int)($_POST['loja_id'] ?? 0),
        'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
        'nome' => $_POST['nome'] ?? '',
        'preco' => $_POST['preco'] ?? 0,
        'imagem' => $_POST['imagem'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'em_promocao' => $em_promocao,
        'porcentagem_promocao' => $em_promocao ? $porcentagem_promocao : 0
    ];

    if (!empty($_POST['id'])) {
        Produto::update((int)$_POST['id'], $data);
    } else {
        Produto::create($data);
    }

    header('Location: produtos.php');
    exit;
}

if (isset($_GET['delete'])) {
    Produto::delete((int)$_GET['delete']);
    header('Location: produtos.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = Produto::find((int)$_GET['edit']);
}

$produtos = Produto::all();
$lojistas = Loja::all();
$categorias = Categoria::all();
$page_title = 'Admin - Produtos';
$page_description = 'Cadastro de produtos';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <h1>Produtos</h1>

    <form class="form" method="post">
      <input type="hidden" name="id" value="<?php echo e($edit['id'] ?? ''); ?>">
      <label>Nome
        <input type="text" name="nome" value="<?php echo e($edit['nome'] ?? ''); ?>" required>
      </label>
      <label>Preco
        <input type="number" step="0.01" name="preco" value="<?php echo e($edit['preco'] ?? ''); ?>" required>
      </label>
      <label>Imagem (URL)
        <input type="text" name="imagem" value="<?php echo e($edit['imagem'] ?? ''); ?>">
      </label>
      <label>Descricao
        <textarea name="descricao" rows="3"><?php echo e($edit['descricao'] ?? ''); ?></textarea>
      </label>
      <label>Lojista
        <select name="loja_id" required>
          <option value="">Selecione</option>
          <?php foreach ($lojistas as $l): ?>
            <option value="<?php echo e($l['id']); ?>" <?php echo ($edit && (int)$edit['loja_id'] === (int)$l['id']) ? 'selected' : ''; ?>><?php echo e($l['nome']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Categoria
        <select name="categoria_id" required>
          <option value="">Selecione</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?php echo e($c['id']); ?>" <?php echo ($edit && (int)$edit['categoria_id'] === (int)$c['id']) ? 'selected' : ''; ?>><?php echo e($c['nome']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        <input type="checkbox" name="em_promocao" value="1" <?php echo (!empty($edit['em_promocao'])) ? 'checked' : ''; ?>>
        Produto em promocao
      </label>
      <label>Porcentagem da promocao (%)
        <input type="number" name="porcentagem_promocao" min="0" max="99" value="<?php echo e((int)($edit['porcentagem_promocao'] ?? 0)); ?>">
      </label>
      <button class="btn" type="submit">Salvar</button>
    </form>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Loja</th>
            <th>Preco</th>
            <th>Promocao</th>
            <th>Acoes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($produtos as $p): ?>
            <tr>
              <td><?php echo e($p['nome']); ?></td>
              <td><?php echo e($p['loja_nome']); ?></td>
              <td><?php echo e(format_price($p['preco'])); ?></td>
              <td><?php echo !empty($p['em_promocao']) ? ('-' . e((int)$p['porcentagem_promocao']) . '%') : 'Nao'; ?></td>
              <td class="table-actions">
                <a class="action-link edit" href="produtos.php?edit=<?php echo e($p['id']); ?>">Editar</a>
                <a class="action-link delete" href="produtos.php?delete=<?php echo e($p['id']); ?>" onclick="return confirm('Excluir produto?');">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once '../includes/footer.php'; ?>
