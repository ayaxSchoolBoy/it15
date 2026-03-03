<?php
// admin_guard.php
// Protect API endpoints for authenticated admins

session_start();

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode([
        'status' => 'INVALID',
        'message' => 'Unauthorized.'
    ]);
    exit;
}
