<?php
require_once __DIR__ . '/../config/database.php';

class Produto {
    private static function normalizePromo($row) {
        $preco = (float)($row['preco'] ?? 0);
        $tipo = $row['tipo_desconto'] ?? 'nenhum';
        $valor = (float)($row['valor_desconto'] ?? 0);

        if (($tipo === '' || $tipo === 'nenhum') && !empty($row['em_promocao']) && (float)($row['porcentagem_promocao'] ?? 0) > 0) {
            $tipo = 'percentual';
            $valor = (float)$row['porcentagem_promocao'];
        }

        $data_fim = $row['data_fim_promocao'] ?? null;
        $promo_valida = false;
        if ($tipo !== 'nenhum' && $valor > 0) {
            $promo_valida = true;
            if (!empty($data_fim)) {
                $fim = DateTime::createFromFormat('Y-m-d', $data_fim);
                if ($fim && $fim < new DateTime('today')) {
                    $promo_valida = false;
                }
            }
        }

        $preco_final = $preco;
        if ($promo_valida) {
            if ($tipo === 'percentual') {
                $preco_final = $preco * (1 - ($valor / 100));
            } elseif ($tipo === 'valor') {
                $preco_final = $preco - $valor;
            }
        }

        if ($preco_final < 0) {
            $preco_final = 0;
        }

        $row['tipo_desconto'] = $tipo ?: 'nenhum';
        $row['valor_desconto'] = $valor;
        $row['promo_ativa'] = $promo_valida ? 1 : 0;
        $row['preco_final'] = round($preco_final, 2);

        return $row;
    }

    private static function normalizeRows($rows) {
        $out = [];
        foreach ($rows as $row) {
            $out[] = self::normalizePromo($row);
        }
        return $out;
    }

    public static function all($categoria_id = null, $q = null, $somente_promocoes = false, $subcategoria_id = null) {
        global $pdo;
        $where = [];
        $params = [];

        if ($somente_promocoes) {
            $where[] = "((p.tipo_desconto IS NOT NULL AND p.tipo_desconto <> 'nenhum' AND p.valor_desconto > 0) OR (p.em_promocao = 1 AND p.porcentagem_promocao > 0))";
            $where[] = '(p.data_fim_promocao IS NULL OR p.data_fim_promocao >= CURDATE())';
        }

        if ($categoria_id) {
            $where[] = 'p.categoria_id = ?';
            $params[] = $categoria_id;
        }

        if ($subcategoria_id) {
            $where[] = 'p.subcategoria_id = ?';
            $params[] = $subcategoria_id;
        }

        if ($q) {
            $where[] = '(p.nome LIKE ? OR p.descricao LIKE ? OR l.nome LIKE ? OR c.nome LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql = 'SELECT p.*, l.nome AS loja_nome, l.whatsapp AS loja_whatsapp, c.nome AS categoria_nome, sc.nome AS subcategoria_nome
                FROM produtos p
                LEFT JOIN lojas l ON l.id = p.loja_id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN subcategorias sc ON sc.id = p.subcategoria_id';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY p.id DESC';

        if ($params) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return self::normalizeRows($stmt->fetchAll());
        }

        return self::normalizeRows($pdo->query($sql)->fetchAll());
    }

    public static function find($id) {
        global $pdo;
        $sql = 'SELECT p.*, l.nome AS loja_nome, l.whatsapp AS loja_whatsapp, c.nome AS categoria_nome, sc.nome AS subcategoria_nome
                FROM produtos p
                LEFT JOIN lojas l ON l.id = p.loja_id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN subcategorias sc ON sc.id = p.subcategoria_id
                WHERE p.id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::normalizePromo($row) : null;
    }

    public static function byLoja($loja_id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM produtos WHERE loja_id = ? ORDER BY id DESC');
        $stmt->execute([$loja_id]);
        return self::normalizeRows($stmt->fetchAll());
    }

    public static function byLojaAdmin($loja_id) {
        global $pdo;
        $sql = 'SELECT p.*, c.nome AS categoria_nome, sc.nome AS subcategoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN subcategorias sc ON sc.id = p.subcategoria_id
                WHERE p.loja_id = ?
                ORDER BY p.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$loja_id]);
        return self::normalizeRows($stmt->fetchAll());
    }

    public static function countByLojas($loja_ids) {
        global $pdo;
        if (!is_array($loja_ids) || !$loja_ids) {
            return [];
        }
        $clean = [];
        foreach ($loja_ids as $id) {
            $id = (int)$id;
            if ($id > 0) {
                $clean[] = $id;
            }
        }
        $clean = array_values(array_unique($clean));
        if (!$clean) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($clean), '?'));
        $stmt = $pdo->prepare("SELECT loja_id, COUNT(*) AS total FROM produtos WHERE loja_id IN ({$placeholders}) GROUP BY loja_id");
        $stmt->execute($clean);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[(int)$row['loja_id']] = (int)$row['total'];
        }
        return $out;
    }

    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO produtos (loja_id, categoria_id, subcategoria_id, nome, preco, imagem, descricao, em_promocao, porcentagem_promocao, tipo_desconto, valor_desconto, data_fim_promocao, preco_alternativo, texto_alternativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['loja_id'],
            $data['categoria_id'],
            $data['subcategoria_id'],
            $data['nome'],
            $data['preco'],
            $data['imagem'],
            $data['descricao'],
            $data['em_promocao'],
            $data['porcentagem_promocao'],
            $data['tipo_desconto'],
            $data['valor_desconto'],
            $data['data_fim_promocao'],
            $data['preco_alternativo'],
            $data['texto_alternativo']
        ]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE produtos SET loja_id = ?, categoria_id = ?, subcategoria_id = ?, nome = ?, preco = ?, imagem = ?, descricao = ?, em_promocao = ?, porcentagem_promocao = ?, tipo_desconto = ?, valor_desconto = ?, data_fim_promocao = ?, preco_alternativo = ?, texto_alternativo = ? WHERE id = ?');
        return $stmt->execute([
            $data['loja_id'],
            $data['categoria_id'],
            $data['subcategoria_id'],
            $data['nome'],
            $data['preco'],
            $data['imagem'],
            $data['descricao'],
            $data['em_promocao'],
            $data['porcentagem_promocao'],
            $data['tipo_desconto'],
            $data['valor_desconto'],
            $data['data_fim_promocao'],
            $data['preco_alternativo'],
            $data['texto_alternativo'],
            $id
        ]);
    }

    public static function delete($id) {
        global $pdo;
        return $pdo->prepare('DELETE FROM produtos WHERE id = ?')->execute([$id]);
    }

    public static function count() {
        global $pdo;
        return (int)$pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
    }
}
