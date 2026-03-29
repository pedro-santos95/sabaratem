<?php
require_once __DIR__ . '/../config/database.php';


class Loja {
    public static function all() {
        global $pdo;
        return $pdo->query('SELECT * FROM lojas ORDER BY id DESC')->fetchAll();
    }

    public static function byIds($ids) {
        global $pdo;
        if (!is_array($ids)) {
            return [];
        }
        $clean = [];
        foreach ($ids as $id) {
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
        $stmt = $pdo->prepare("SELECT * FROM lojas WHERE id IN ({$placeholders})");
        $stmt->execute($clean);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[(int)$row['id']] = $row;
        }
        return $out;
    }

    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM lojas WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO lojas (nome, whatsapp, logo, endereco, descricao, telefone, horario) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['nome'],
            $data['whatsapp'],
            $data['logo'],
            $data['endereco'],
            $data['descricao'],
            $data['telefone'],
            $data['horario']
        ]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE lojas SET nome = ?, whatsapp = ?, logo = ?, endereco = ?, descricao = ?, telefone = ?, horario = ? WHERE id = ?');
        return $stmt->execute([
            $data['nome'],
            $data['whatsapp'],
            $data['logo'],
            $data['endereco'],
            $data['descricao'],
            $data['telefone'],
            $data['horario'],
            $id
        ]);
    }

    public static function delete($id) {
        global $pdo;
        $pdo->prepare('DELETE FROM produtos WHERE loja_id = ?')->execute([$id]);
        return $pdo->prepare('DELETE FROM lojas WHERE id = ?')->execute([$id]);
    }

    public static function count() {
        global $pdo;
        return (int)$pdo->query('SELECT COUNT(*) FROM lojas')->fetchColumn();
    }
}
