<?php
require_once __DIR__ . '/../config/database.php';

class Metricas {
    private static function ensureTable() {
        global $pdo;
        $pdo->exec('CREATE TABLE IF NOT EXISTS metricas (
            id INT PRIMARY KEY,
            vendas_whatsapp INT NOT NULL DEFAULT 0
        )');
    }

    private static function ensureRow() {
        global $pdo;
        self::ensureTable();
        $stmt = $pdo->query('SELECT id FROM metricas WHERE id = 1');
        $row = $stmt->fetch();
        if (!$row) {
            $pdo->prepare('INSERT INTO metricas (id, vendas_whatsapp) VALUES (1, 0)')->execute();
        }
    }

    public static function getWhatsappSales() {
        global $pdo;
        self::ensureRow();
        $stmt = $pdo->query('SELECT vendas_whatsapp FROM metricas WHERE id = 1');
        $value = $stmt->fetchColumn();
        return (int)$value;
    }

    public static function incrementWhatsappSales($count) {
        global $pdo;
        $count = (int)$count;
        if ($count < 1) {
            return;
        }
        self::ensureRow();
        $stmt = $pdo->prepare('UPDATE metricas SET vendas_whatsapp = vendas_whatsapp + ? WHERE id = 1');
        $stmt->execute([$count]);
    }
}
