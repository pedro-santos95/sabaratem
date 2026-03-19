<?php
require_once __DIR__ . '/../config/database.php';

class Subcategoria {
    public static function all() {
        global $pdo;
        $sql = 'SELECT s.*, c.nome AS categoria_nome
                FROM subcategorias s
                INNER JOIN categorias c ON c.id = s.categoria_id
                ORDER BY c.nome ASC, s.nome ASC';
        return $pdo->query($sql)->fetchAll();
    }

    public static function byCategoria($categoria_id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM subcategorias WHERE categoria_id = ? ORDER BY nome ASC');
        $stmt->execute([$categoria_id]);
        return $stmt->fetchAll();
    }

    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM subcategorias WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO subcategorias (categoria_id, nome, imagem) VALUES (?, ?, ?)');
        $stmt->execute([$data['categoria_id'], $data['nome'], $data['imagem'] ?? '']);
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE subcategorias SET categoria_id = ?, nome = ?, imagem = ? WHERE id = ?');
        return $stmt->execute([$data['categoria_id'], $data['nome'], $data['imagem'] ?? '', $id]);
    }

    public static function delete($id) {
        global $pdo;
        $pdo->prepare('UPDATE produtos SET subcategoria_id = NULL WHERE subcategoria_id = ?')->execute([$id]);
        return $pdo->prepare('DELETE FROM subcategorias WHERE id = ?')->execute([$id]);
    }
}

