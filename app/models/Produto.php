<?php
require_once __DIR__ . '/../config/database.php';


class Produto {
    public static function all($categoria_id = null, $q = null, $somente_promocoes = false) {
        global $pdo;
        $where = [];
        $params = [];

        if ($somente_promocoes) {
            $where[] = 'p.em_promocao = 1';
            $where[] = 'p.porcentagem_promocao > 0';
        }

        if ($categoria_id) {
            $where[] = 'p.categoria_id = ?';
            $params[] = $categoria_id;
        }

        if ($q) {
            $where[] = 'p.nome LIKE ?';
            $params[] = '%' . $q . '%';
        }

        $sql = 'SELECT p.*, l.nome AS loja_nome, l.whatsapp AS loja_whatsapp,
                CASE
                    WHEN p.em_promocao = 1 AND p.porcentagem_promocao > 0
                    THEN ROUND(p.preco * (1 - (p.porcentagem_promocao / 100)), 2)
                    ELSE p.preco
                END AS preco_final
                FROM produtos p
                LEFT JOIN lojas l ON l.id = p.loja_id';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY p.id DESC';

        if ($params) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        return $pdo->query($sql)->fetchAll();
    }

    public static function find($id) {
        global $pdo;
        $sql = 'SELECT p.*, l.nome AS loja_nome, l.whatsapp AS loja_whatsapp,
                CASE
                    WHEN p.em_promocao = 1 AND p.porcentagem_promocao > 0
                    THEN ROUND(p.preco * (1 - (p.porcentagem_promocao / 100)), 2)
                    ELSE p.preco
                END AS preco_final
                FROM produtos p
                LEFT JOIN lojas l ON l.id = p.loja_id
                WHERE p.id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function byLoja($loja_id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM produtos WHERE loja_id = ? ORDER BY id DESC');
        $stmt->execute([$loja_id]);
        return $stmt->fetchAll();
    }

    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO produtos (loja_id, categoria_id, nome, preco, imagem, descricao, em_promocao, porcentagem_promocao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['loja_id'],
            $data['categoria_id'],
            $data['nome'],
            $data['preco'],
            $data['imagem'],
            $data['descricao'],
            $data['em_promocao'],
            $data['porcentagem_promocao']
        ]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE produtos SET loja_id = ?, categoria_id = ?, nome = ?, preco = ?, imagem = ?, descricao = ?, em_promocao = ?, porcentagem_promocao = ? WHERE id = ?');
        return $stmt->execute([
            $data['loja_id'],
            $data['categoria_id'],
            $data['nome'],
            $data['preco'],
            $data['imagem'],
            $data['descricao'],
            $data['em_promocao'],
            $data['porcentagem_promocao'],
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
