<?php
require_once 'auth.php';
require_once '../app/config/database.php';
require_once '../app/models/Produto.php';
require_once '../app/models/Loja.php';
require_once '../app/models/Categoria.php';
require_once '../app/models/Subcategoria.php';
require_once '../app/helpers/functions.php';

$form_error = null;
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preco = (float)str_replace(',', '.', $_POST['preco'] ?? 0);
    $promo_ativa = (int)($_POST['promocao_ativa'] ?? 0);
    $tipo_desconto = $_POST['tipo_desconto'] ?? 'nenhum';
    $tipo_desconto = in_array($tipo_desconto, ['nenhum', 'percentual', 'valor'], true) ? $tipo_desconto : 'nenhum';
    $valor_desconto = (float)str_replace(',', '.', $_POST['valor_desconto'] ?? 0);
    $data_fim_promocao = trim($_POST['data_fim_promocao'] ?? '');
    $preco_alternativo_raw = trim($_POST['preco_alternativo'] ?? '');
    $preco_alternativo = $preco_alternativo_raw !== '' ? (float)str_replace(',', '.', $preco_alternativo_raw) : null;
    $texto_alternativo = trim($_POST['texto_alternativo'] ?? '');

    if ($promo_ativa !== 1) {
        $tipo_desconto = 'nenhum';
        $valor_desconto = 0;
        $data_fim_promocao = null;
    }

    if ($preco <= 0) {
        $form_error = 'Informe um preço válido.';
    }

    if ($tipo_desconto === 'percentual') {
        if ($valor_desconto <= 0 || $valor_desconto > 100) {
            $form_error = 'Percentual de desconto inválido.';
        }
    }

    if ($tipo_desconto === 'valor') {
        if ($valor_desconto <= 0) {
            $form_error = 'Valor de desconto inválido.';
        } elseif ($preco > 0 && $valor_desconto >= $preco) {
            $form_error = 'O desconto não pode ser maior ou igual ao preço.';
        }
    }

    if ($data_fim_promocao !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $data_fim_promocao);
        if (!$dt || $dt->format('Y-m-d') !== $data_fim_promocao) {
            $form_error = 'Data final da promoção inválida.';
        }
    } else {
        $data_fim_promocao = null;
    }

    if ($preco_alternativo !== null && $preco_alternativo < 0) {
        $form_error = 'Preço alternativo inválido.';
    }

    $imagem = $_POST['imagem_atual'] ?? '';
    $upload = upload_image($_FILES['imagem'] ?? null, 'produtos');
    if ($upload) {
        $imagem = $upload;
    }

    $subcategoria_id = !empty($_POST['subcategoria_id']) ? (int)$_POST['subcategoria_id'] : null;
    $em_promocao = $promo_ativa === 1 ? 1 : 0;
    $porcentagem_promocao = $tipo_desconto === 'percentual' ? $valor_desconto : 0;

    $data = [
        'loja_id' => (int)($_POST['loja_id'] ?? 0),
        'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
        'subcategoria_id' => $subcategoria_id,
        'nome' => $_POST['nome'] ?? '',
        'preco' => $preco,
        'imagem' => $imagem,
        'descricao' => $_POST['descricao'] ?? '',
        'em_promocao' => $em_promocao,
        'porcentagem_promocao' => $porcentagem_promocao,
        'tipo_desconto' => $tipo_desconto,
        'valor_desconto' => $valor_desconto,
        'data_fim_promocao' => $data_fim_promocao,
        'preco_alternativo' => $preco_alternativo,
        'texto_alternativo' => $texto_alternativo
    ];

    if ($form_error) {
        $edit = $data;
        $edit['id'] = $_POST['id'] ?? '';
    } else {
        if (!empty($_POST['id'])) {
            Produto::update((int)$_POST['id'], $data);
        } else {
            Produto::create($data);
        }

        header('Location: produtos.php');
        exit;
    }
}

if (!$edit && isset($_GET['edit'])) {
    $edit = Produto::find((int)$_GET['edit']);
}

$produtos = Produto::all();
$lojistas = Loja::all();
$categorias = Categoria::all();
$subcategorias = Subcategoria::all();

$tipo_desconto_form = $edit['tipo_desconto'] ?? 'nenhum';
$valor_desconto_form = $edit['valor_desconto'] ?? '';
if (($tipo_desconto_form === '' || $tipo_desconto_form === 'nenhum') && !empty($edit['em_promocao']) && (float)($edit['porcentagem_promocao'] ?? 0) > 0) {
    $tipo_desconto_form = 'percentual';
    $valor_desconto_form = (float)$edit['porcentagem_promocao'];
}
$data_fim_form = $edit['data_fim_promocao'] ?? '';
$preco_alt_form = $edit['preco_alternativo'] ?? '';
$texto_alt_form = $edit['texto_alternativo'] ?? '';
$promo_ativa_form = ($tipo_desconto_form !== 'nenhum' && (float)$valor_desconto_form > 0) ? 1 : 0;

$page_title = 'Admin - Produtos';
$page_description = 'Cadastro de produtos';
require_once '../includes/header.php';
?>
<div class="admin-layout">
  <?php require_once '../includes/sidebar.php'; ?>
  <section class="admin-content">
    <h1>Produtos</h1>

    <?php if (!empty($form_error)): ?>
      <div class="form-error"><?php echo e($form_error); ?></div>
    <?php endif; ?>

    <form class="form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?php echo e($edit['id'] ?? ''); ?>">
      <label>Nome
        <input type="text" name="nome" value="<?php echo e($edit['nome'] ?? ''); ?>" required>
      </label>
      <label>Preço
        <input type="number" step="0.01" name="preco" value="<?php echo e($edit['preco'] ?? ''); ?>" required>
      </label>
      <label>Imagem do produto
        <input type="file" name="imagem" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
        <input type="hidden" name="imagem_atual" value="<?php echo e($edit['imagem'] ?? ''); ?>">
        <?php if (!empty($edit['imagem'])): ?>
          <small class="muted">Atual: <?php echo e($edit['imagem']); ?></small>
        <?php endif; ?>
      </label>
      <label>Descrição
        <textarea name="descricao" rows="3"><?php echo e($edit['descricao'] ?? ''); ?></textarea>
      </label>
      <label>Lojista
        <select name="loja_id" required>
          <option value="">Selecione</option>
          <?php foreach ($lojistas as $l): ?>
            <option value="<?php echo e($l['id']); ?>" <?php echo ($edit && (int)($edit['loja_id'] ?? 0) === (int)$l['id']) ? 'selected' : ''; ?>><?php echo e($l['nome']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Categoria
        <select name="categoria_id" required>
          <option value="">Selecione</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?php echo e($c['id']); ?>" <?php echo ($edit && (int)($edit['categoria_id'] ?? 0) === (int)$c['id']) ? 'selected' : ''; ?>><?php echo e($c['nome']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Subcategoria
        <select name="subcategoria_id" id="subcategoria-select">
          <option value="">Selecione</option>
          <?php foreach ($subcategorias as $s): ?>
            <option value="<?php echo e($s['id']); ?>" data-categoria="<?php echo e($s['categoria_id']); ?>" <?php echo ($edit && (int)($edit['subcategoria_id'] ?? 0) === (int)$s['id']) ? 'selected' : ''; ?>><?php echo e($s['nome']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Produto em promoção
        <select name="promocao_ativa" id="promocao-ativa">
          <option value="0" <?php echo ($promo_ativa_form === 0) ? 'selected' : ''; ?>>Não</option>
          <option value="1" <?php echo ($promo_ativa_form === 1) ? 'selected' : ''; ?>>Sim</option>
        </select>
      </label>

      <label id="tipo-desconto-wrap">Tipo de desconto
        <select name="tipo_desconto" id="tipo-desconto">
          <option value="nenhum" <?php echo ($tipo_desconto_form === 'nenhum') ? 'selected' : ''; ?>>Sem desconto</option>
          <option value="percentual" <?php echo ($tipo_desconto_form === 'percentual') ? 'selected' : ''; ?>>Percentual (%)</option>
          <option value="valor" <?php echo ($tipo_desconto_form === 'valor') ? 'selected' : ''; ?>>Valor (R$)</option>
        </select>
      </label>

      <label id="valor-desconto-wrap">Valor do desconto
        <input type="number" step="0.01" name="valor_desconto" id="valor-desconto" value="<?php echo e($valor_desconto_form); ?>">
        <small class="muted" id="valor-desconto-hint">Informe o valor do desconto.</small>
      </label>

      <label id="data-fim-wrap">Validade da promoção
        <input type="date" name="data_fim_promocao" value="<?php echo e($data_fim_form); ?>">
        <small class="muted">Deixe vazio para promoção sem data final.</small>
      </label>

      <label>Preço Alternativo
        <input type="number" step="0.01" name="preco_alternativo" value="<?php echo e($preco_alt_form); ?>">
      </label>

      <label>Texto Alternativo
        <input type="text" name="texto_alternativo" value="<?php echo e($texto_alt_form); ?>" placeholder="Ex: Frete Grátis">
      </label>

      <button class="btn" type="submit">Salvar</button>
    </form>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Subcategoria</th>
            <th>Loja</th>
            <th>Preço</th>
            <th>Promoção</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($produtos as $p): ?>
            <tr>
              <td><?php echo e($p['nome']); ?></td>
              <td><?php echo e($p['subcategoria_nome'] ?? ''); ?></td>
              <td><?php echo e($p['loja_nome']); ?></td>
              <td><?php echo e(format_price($p['preco'])); ?></td>
              <td>
                <?php if (!empty($p['promo_ativa'])): ?>
                  <?php if (($p['tipo_desconto'] ?? '') === 'percentual'): ?>
                    <?php $percent = rtrim(rtrim(number_format((float)$p['valor_desconto'], 2, ',', '.'), '0'), ','); ?>
                    -<?php echo e($percent); ?>%
                  <?php else: ?>
                    -<?php echo e(format_price($p['valor_desconto'])); ?>
                  <?php endif; ?>
                <?php else: ?>
                  Não
                <?php endif; ?>
              </td>
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
<script>
(function () {
  var cat = document.querySelector('select[name="categoria_id"]');
  var sub = document.getElementById('subcategoria-select');
  if (cat && sub) {
    function filter() {
      var catId = cat.value;
      var options = sub.querySelectorAll('option');
      options.forEach(function (opt) {
        if (!opt.value) {
          opt.hidden = false;
          return;
        }
        opt.hidden = !catId || opt.getAttribute('data-categoria') !== catId;
      });

      if (!catId) {
        sub.value = '';
        return;
      }

      var selected = sub.value;
      if (selected) {
        var selectedOpt = sub.querySelector('option[value="' + selected + '"]');
        if (selectedOpt && selectedOpt.hidden) {
          sub.value = '';
        }
      }
    }

    cat.addEventListener('change', filter);
    filter();
  }

  var promo = document.getElementById('promocao-ativa');
  var tipo = document.getElementById('tipo-desconto');
  var tipoWrap = document.getElementById('tipo-desconto-wrap');
  var valorWrap = document.getElementById('valor-desconto-wrap');
  var valorHint = document.getElementById('valor-desconto-hint');
  var dataWrap = document.getElementById('data-fim-wrap');

  function toggleDesconto() {
    var promoAtiva = promo && promo.value === '1';
    var value = tipo ? tipo.value : 'nenhum';
    if (!promoAtiva) {
      if (tipo) {
        tipo.value = 'nenhum';
      }
      value = 'nenhum';
    }

    if (tipoWrap) {
      tipoWrap.style.display = promoAtiva ? 'grid' : 'none';
    }
    if (valorWrap) {
      valorWrap.style.display = promoAtiva ? 'grid' : 'none';
    }
    if (dataWrap) {
      dataWrap.style.display = promoAtiva ? 'grid' : 'none';
    }
    if (valorHint) {
      if (value === 'percentual') {
        valorHint.textContent = 'Valor em %.';
      } else if (value === 'valor') {
        valorHint.textContent = 'Valor em R$.';
      } else {
        valorHint.textContent = 'Informe o valor do desconto.';
      }
    }
  }

  if (promo) {
    promo.addEventListener('change', toggleDesconto);
  }
  if (tipo) {
    tipo.addEventListener('change', toggleDesconto);
  }
  toggleDesconto();
})();
</script>
<?php require_once '../includes/footer.php'; ?>




