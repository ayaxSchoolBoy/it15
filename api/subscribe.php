<?php
// subscribe.php
// Handles new membership subscription
// Generates membership code + QR image
// Stores data in MySQL and emails QR code

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

session_start();

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
    http_response_code(401);
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Please login as a customer to subscribe.'
    ]);
    exit;
}

require __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';
require __DIR__ . '/membership.php';

// Check dependencies (QR library)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'QR library not installed. Run composer install.'
    ]);
    exit;
}

require $autoload;

// Read POST data
$memberName = trim($_SESSION['user_name'] ?? '');
$email = trim($_SESSION['user_email'] ?? '');
$plan = trim($_POST['plan'] ?? '');

if ($memberName === '' || $email === '' || $plan === '') {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'All fields are required.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Invalid email address.'
    ]);
    exit;
}

// Direct (non-payment) membership creation
$result = createMembership($pdo, $config, (int)$_SESSION['user_id'], $memberName, $email, $plan);
echo json_encode($result);
