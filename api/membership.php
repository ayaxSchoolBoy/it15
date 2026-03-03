<?php
// membership.php
// Shared membership creation logic (QR generation + email)

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function createMembership(PDO $pdo, array $config, int $userId, string $memberName, string $email, string $plan): array {
    // Determine expiration based on plan
    $today = new DateTime('today');
    $expiration = clone $today;

    switch (strtolower($plan)) {
        case 'monthly':
            $expiration->modify('+1 month');
            break;
        case 'quarterly':
            $expiration->modify('+3 months');
            break;
        case 'annual':
            $expiration->modify('+1 year');
            break;
        default:
            return [
                'status' => 'ERROR',
                'message' => 'Invalid plan selected.'
            ];
    }

    $expirationDate = $expiration->format('Y-m-d');

    // Generate unique membership code
    $year = date('Y');
    do {
        $random = str_pad((string)rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $membershipCode = "MEM-$year-$random";

        $stmt = $pdo->prepare('SELECT id FROM memberships WHERE membership_code = ? LIMIT 1');
        $stmt->execute([$membershipCode]);
        $exists = $stmt->fetch();
    } while ($exists);

    // Insert into database
    $stmt = $pdo->prepare('INSERT INTO memberships (user_id, membership_code, member_name, email, plan, expiration_date, status, qr_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $membershipCode, $memberName, $email, $plan, $expirationDate, 'ACTIVE', null]);

    // Generate QR code image
    $qrDir = __DIR__ . '/../uploads/qrcodes';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0777, true);
    }

    $svgPath = $qrDir . '/' . $membershipCode . '.svg';
    $pngPath = $qrDir . '/' . $membershipCode . '.png';

    // Always generate SVG (no GD dependency)
    $svgOptions = new QROptions([
        'outputType' => QRCode::OUTPUT_MARKUP_SVG,
        'scale' => 6,
    ]);

    $svgCode = new QRCode($svgOptions);
    $svgData = $svgCode->render($membershipCode);

    if (strpos($svgData, 'data:image/svg+xml;base64,') === 0) {
        $svgData = base64_decode(substr($svgData, strlen('data:image/svg+xml;base64,')));
    } elseif (strpos($svgData, 'data:image/svg+xml;utf8,') === 0) {
        $svgData = urldecode(substr($svgData, strlen('data:image/svg+xml;utf8,')));
    }

    file_put_contents($svgPath, $svgData);

    // Try to generate PNG if GD is available (better email preview)
    if (extension_loaded('gd')) {
        $pngOptions = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 8,
        ]);

        $pngCode = new QRCode($pngOptions);
        $pngData = $pngCode->render($membershipCode);

        if (strpos($pngData, 'data:image/png;base64,') === 0) {
            $pngData = base64_decode(substr($pngData, strlen('data:image/png;base64,')));
        }

        file_put_contents($pngPath, $pngData);
    }

    // Send email (SMTP)
    $emailSent = false;
    $emailError = '';

    if (!empty($config['mail_enabled'])) {
        try {
            $mail = new PHPMailer(true);

            $smtp = $config['smtp'] ?? [];

            $mail->isSMTP();
            $mail->Host = $smtp['host'] ?? '';
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['username'] ?? '';
            $mail->Password = $smtp['password'] ?? '';
            $mail->SMTPSecure = $smtp['encryption'] ?? 'tls';
            $mail->Port = $smtp['port'] ?? 587;

            $mail->setFrom($config['mail_from'], 'Gym Membership');
            $mail->addAddress($email, $memberName);

            $mail->Subject = 'Your Gym Membership QR Code';
            $mail->Body = "Hello $memberName,\n\nHere is your membership QR code: $membershipCode\nPlease show it during check-in.\n\nThank you!";

            $emailPath = file_exists($pngPath) ? $pngPath : $svgPath;
            $attachmentName = pathinfo($emailPath, PATHINFO_BASENAME);
            $mail->addAttachment($emailPath, $attachmentName);

            $emailSent = $mail->send();
        } catch (Exception $e) {
            $emailSent = false;
            $emailError = $e->getMessage();
        }
    }

    $baseUrl = rtrim($config['base_url'], '/');
    $qrUrl = $baseUrl . '/uploads/qrcodes/' . $membershipCode . (file_exists($pngPath) ? '.png' : '.svg');

    $stmt = $pdo->prepare('UPDATE memberships SET qr_path = ? WHERE membership_code = ?');
    $stmt->execute([$qrUrl, $membershipCode]);

    return [
        'status' => 'OK',
        'membership_code' => $membershipCode,
        'expiration_date' => $expirationDate,
        'email_status' => $emailSent ? 'SENT' : ($config['mail_enabled'] ? 'FAILED' : 'DISABLED'),
        'email_error' => $emailSent ? '' : $emailError,
        'qr_url' => $qrUrl,
    ];
}
