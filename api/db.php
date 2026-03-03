<?php
// db.php
// Database connection using PDO (update credentials as needed)

$config = require __DIR__ . '/config.php';

$host = $config['db']['host'];
$dbname = $config['db']['name'];
$user = $config['db']['user'];
$pass = $config['db']['pass'];
$charset = $config['db']['charset'];

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'INVALID',
        'message' => 'Database connection failed.'
    ]);
    exit;
}
