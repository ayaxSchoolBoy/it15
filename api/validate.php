<?php
// validate.php
// Receives QR code value via POST (JSON)
// Returns JSON response

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

// Simple rate limiting (per session, 10 requests per minute)
if (!isset($_SESSION['scan_rate'])) {
    $_SESSION['scan_rate'] = ['start' => time(), 'count' => 0];
}

$elapsed = time() - $_SESSION['scan_rate']['start'];
if ($elapsed > 60) {
    $_SESSION['scan_rate'] = ['start' => time(), 'count' => 0];
}

$_SESSION['scan_rate']['count']++;
if ($_SESSION['scan_rate']['count'] > 10) {
    http_response_code(429);
    echo json_encode([
        'status' => 'INVALID',
        'message' => 'Too many requests. Please wait.'
    ]);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$qrValue = $input['qr_value'] ?? '';
$qrValue = is_string($qrValue) ? trim($qrValue) : '';

if (empty($qrValue) || strlen($qrValue) > 100) {
    echo json_encode([
        'status' => 'INVALID',
        'message' => 'QR value missing.'
    ]);
    exit;
}

require __DIR__ . '/db.php';

// Query membership by code
$stmt = $pdo->prepare('SELECT member_name, plan, expiration_date, status FROM memberships WHERE membership_code = ? LIMIT 1');
$stmt->execute([$qrValue]);
$member = $stmt->fetch();

if (!$member) {
    $stmt = $pdo->prepare('INSERT INTO scan_logs (membership_code, result_status, ip_address) VALUES (?, ?, ?)');
    $stmt->execute([$qrValue, 'INVALID', $_SERVER['REMOTE_ADDR'] ?? 'unknown']);

    echo json_encode([
        'status' => 'INVALID',
        'message' => 'Membership not found.'
    ]);
    exit;
}

// Determine if expired
$today = new DateTime('today');
$expiration = new DateTime($member['expiration_date']);
$isExpired = $expiration < $today;

// Determine final status
$finalStatus = 'ACTIVE';
if ($member['status'] !== 'ACTIVE' || $isExpired) {
    $finalStatus = 'EXPIRED';
}

// Log scan
$stmt = $pdo->prepare('INSERT INTO scan_logs (membership_code, result_status, ip_address) VALUES (?, ?, ?)');
$stmt->execute([$qrValue, $finalStatus, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);

echo json_encode([
    'member_name' => $member['member_name'],
    'plan' => $member['plan'],
    'expiration_date' => $member['expiration_date'],
    'status' => $finalStatus
]);
