<?php
require_once __DIR__ . '/../config/database.php';


class Categoria {
    public static function all() {
        global $pdo;
        return $pdo->query('SELECT * FROM categorias ORDER BY nome ASC')->fetchAll();
    }

    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO categorias (nome, svg) VALUES (?, ?)');
        $stmt->execute([$data['nome'], $data['svg']]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE categorias SET nome = ?, svg = ? WHERE id = ?');
        return $stmt->execute([$data['nome'], $data['svg'], $id]);
    }

    public static function delete($id) {
        global $pdo;
        $pdo->prepare('UPDATE produtos SET categoria_id = NULL WHERE categoria_id = ?')->execute([$id]);
        return $pdo->prepare('DELETE FROM categorias WHERE id = ?')->execute([$id]);
    }
}
