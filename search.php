<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare('SELECT id, nome, imagem FROM produtos WHERE nome LIKE ? ORDER BY nome ASC LIMIT 8');
$stmt->execute(['%' . $q . '%']);
$rows = $stmt->fetchAll();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
?>
