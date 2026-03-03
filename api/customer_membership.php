<?php
header('Content-Type: application/json');

session_start();

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
    http_response_code(401);
    echo json_encode(['status' => 'ERROR', 'message' => 'Unauthorized.']);
    exit;
}

require __DIR__ . '/db.php';

$stmt = $pdo->prepare('SELECT membership_code, plan, expiration_date, status, qr_path FROM memberships WHERE user_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$_SESSION['user_id']]);
$membership = $stmt->fetch();

if (!$membership) {
    echo json_encode(['status' => 'ERROR', 'message' => 'No membership found.']);
    exit;
}

echo json_encode([
    'status' => 'OK',
    'membership_code' => $membership['membership_code'],
    'plan' => $membership['plan'],
    'expiration_date' => $membership['expiration_date'],
    'status' => $membership['status'],
    'qr_url' => $membership['qr_path'],
]);
