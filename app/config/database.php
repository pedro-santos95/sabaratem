<?php
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true)
    || PHP_SAPI === 'cli'
    || (($_SERVER['SERVER_NAME'] ?? '') === 'localhost');

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: ($isLocal ? 'sabaratem' : 'u318929259_sabara2026');
$DB_USER = getenv('DB_USER') ?: ($isLocal ? 'root' : 'u318929259_Sabara');
$DB_PASS = getenv('DB_PASSWORD');
if ($DB_PASS === false) {
    $DB_PASS = $isLocal ? '' : '36747132Ph';
}
$DB_CHARSET = getenv('DB_CHARSET') ?: 'utf8mb4';

// Ajuste para depuracao: defina false quando finalizar.
$SHOW_DB_ERRORS = true;

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log('DB ERROR: ' . $e->getMessage());
    if ($SHOW_DB_ERRORS) {
        die('Erro ao conectar no banco de dados. Detalhes: ' . $e->getMessage());
    }
    die('Erro ao conectar no banco de dados.');
}
?>
