<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Produto.php';
require_once BASE_PATH . '/app/helpers/functions.php';

$categoria_id = (int)($_GET['categoria_id'] ?? 0);
$promo = (int)($_GET['promo'] ?? 0) === 1;
$q = trim($_GET['q'] ?? '');
$produtos = Produto::all($categoria_id ?: null, $q ?: null, $promo);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($produtos);
?>
