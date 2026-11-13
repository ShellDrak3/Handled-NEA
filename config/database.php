<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

// Local database credentials.
$host = '127.0.0.1';
$dbname = 'handled_nea';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Create a PDO connection with error handling.
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $exception) {
    // Avoid exposing database details to users.
    die('Database connection failed.');
}
