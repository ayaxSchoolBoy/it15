<?php
session_start();
require __DIR__ . '/api/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $exists = $stmt->fetch();

        if ($exists) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hash, 'customer']);
            $success = 'Registration successful. You can now login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Gym Membership</title>
  <link rel="stylesheet" href="/it15/admin/assets/login.css?v=7" />
</head>
<body>
  <div class="page">
    <div class="card">
      <div class="brand">
        <span class="brand-mark"></span>
        <span class="brand-name">PinoyFlex</span>
      </div>
      <h1>Register</h1>
      <p class="muted">Create your customer account.</p>

      <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form method="POST">
        <label>
          Full Name
          <input type="text" name="name" required />
        </label>

        <label>
          Email
          <input type="email" name="email" required />
        </label>

        <label>
          Password
          <input type="password" name="password" required />
        </label>

        <button type="submit">Register</button>
      </form>

      <p class="muted" style="margin-top: 12px;">
        Already registered? <a href="login.php" class="link">Login here</a>
      </p>
    </div>
  </div>
</body>
</html>
