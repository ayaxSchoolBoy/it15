<?php
// members.php
// Admin member listing and status updates

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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = trim($_GET['search'] ?? '');

    if ($search !== '') {
        $like = '%' . $search . '%';
        $stmt = $pdo->prepare('SELECT membership_code, member_name, plan, expiration_date, status FROM memberships WHERE member_name LIKE ? OR membership_code LIKE ? ORDER BY created_at DESC LIMIT 100');
        $stmt->execute([$like, $like]);
    } else {
        $stmt = $pdo->query('SELECT membership_code, member_name, plan, expiration_date, status FROM memberships ORDER BY created_at DESC LIMIT 100');
    }

    echo json_encode([
        'status' => 'OK',
        'members' => $stmt->fetchAll()
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $code = trim($input['membership_code'] ?? '');
    $status = trim($input['status'] ?? '');

    if ($code === '' || !in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
        echo json_encode([
            'status' => 'INVALID',
            'message' => 'Invalid request.'
        ]);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE memberships SET status = ? WHERE membership_code = ?');
    $stmt->execute([$status, $code]);

    echo json_encode([
        'status' => 'OK'
    ]);
    exit;
}

http_response_code(405);
echo json_encode([
    'status' => 'INVALID',
    'message' => 'Method not allowed.'
]);
