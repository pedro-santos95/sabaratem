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

$terms = preg_split('/\s+/', mb_strtolower($q, 'UTF-8'));
$terms = array_values(array_filter($terms, function ($term) {
    return mb_strlen($term, 'UTF-8') >= 2;
}));

$where = [];
$params = [];

if (!$terms) {
    $terms = [mb_strtolower($q, 'UTF-8')];
}

foreach ($terms as $term) {
    $like = '%' . $term . '%';
    $where[] = '(LOWER(p.nome) LIKE ? OR LOWER(COALESCE(p.descricao, "")) LIKE ? OR LOWER(COALESCE(l.nome, "")) LIKE ? OR LOWER(COALESCE(c.nome, "")) LIKE ?)';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$full = mb_strtolower($q, 'UTF-8');
$scoreStarts = $full . '%';
$scoreLike = '%' . $full . '%';

$sql = 'SELECT
            p.id,
            p.nome,
            p.imagem,
            l.nome AS loja_nome,
            c.nome AS categoria_nome,
            (
                CASE WHEN LOWER(p.nome) LIKE ? THEN 40 ELSE 0 END +
                CASE WHEN LOWER(p.nome) LIKE ? THEN 20 ELSE 0 END +
                CASE WHEN LOWER(COALESCE(l.nome, "")) LIKE ? THEN 10 ELSE 0 END +
                CASE WHEN LOWER(COALESCE(c.nome, "")) LIKE ? THEN 10 ELSE 0 END +
                CASE WHEN LOWER(COALESCE(p.descricao, "")) LIKE ? THEN 5 ELSE 0 END
            ) AS score
        FROM produtos p
        LEFT JOIN lojas l ON l.id = p.loja_id
        LEFT JOIN categorias c ON c.id = p.categoria_id
        WHERE ' . implode(' AND ', $where) . '
        ORDER BY score DESC, p.nome ASC
        LIMIT 8';

$stmt = $pdo->prepare($sql);
$stmt->execute(array_merge(
    [$scoreStarts, $scoreLike, $scoreLike, $scoreLike, $scoreLike],
    $params
));
$rows = $stmt->fetchAll();

echo json_encode([
    'query' => $q,
    'count' => count($rows),
    'items' => $rows,
]);
?>
