<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Metricas.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método não permitido']);
    exit;
}

$count = (int)($_POST['count'] ?? 0);
if ($count < 1) {
    echo json_encode(['ok' => false, 'error' => 'Contagem inválida']);
    exit;
}

Metricas::incrementWhatsappSales($count);
echo json_encode(['ok' => true]);
