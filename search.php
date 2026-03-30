<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/config/database.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode([
        'query' => '',
        'count' => 0,
        'items' => [],
    ]);
    exit;
}

$term = mb_strtolower($q, 'UTF-8');
$like = '%' . $term . '%';
$fuzzy = '';
if (mb_strlen($term, 'UTF-8') >= 4) {
    $chars = preg_split('//u', $term, -1, PREG_SPLIT_NO_EMPTY);
    if ($chars) {
        $fuzzy = '%' . implode('%', $chars) . '%';
    }
}

$where = 'LOWER(p.nome) LIKE ?
           OR LOWER(COALESCE(c.nome, "")) LIKE ?
           OR LOWER(COALESCE(sc.nome, "")) LIKE ?';
$params = [$like, $like, $like];

if ($fuzzy !== '') {
    $where .= ' OR LOWER(p.nome) LIKE ?
               OR LOWER(COALESCE(c.nome, "")) LIKE ?
               OR LOWER(COALESCE(sc.nome, "")) LIKE ?';
    $params = array_merge($params, [$fuzzy, $fuzzy, $fuzzy]);
}

$sql = 'SELECT p.id, p.nome
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        LEFT JOIN subcategorias sc ON sc.id = p.subcategoria_id
        WHERE ' . $where . '
        ORDER BY p.nome ASC
        LIMIT 12';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

echo json_encode([
    'query' => $q,
    'count' => count($rows),
    'items' => $rows,
]);
?>
