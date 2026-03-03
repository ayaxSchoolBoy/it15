<?php
// history.php
// Returns last 20 scan records for admin history

header('Content-Type: application/json');

require __DIR__ . '/admin_guard.php';

// CSRF validation
$csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfHeader)) {
    http_response_code(403);
    echo json_encode([
        'status' => 'INVALID',
        'message' => 'Invalid CSRF token.'
    ]);
    exit;
}

require __DIR__ . '/db.php';

$stmt = $pdo->query('SELECT membership_code, result_status, scanned_at, ip_address FROM scan_logs ORDER BY scanned_at DESC LIMIT 20');
$rows = $stmt->fetchAll();

echo json_encode([
    'status' => 'OK',
    'history' => $rows
]);
