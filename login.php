<?php
session_start();
require __DIR__ . '/api/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php');
                exit;
            }

              header('Location: customer_dashboard.php');
            exit;
        }

        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Gym Membership</title>
  <link rel="stylesheet" href="/it15/admin/assets/login.css?v=7" />
</head>
<body>
  <div class="page">
    <div class="card">
      <div class="brand">
        <span class="brand-mark"></span>
        <span class="brand-name">PinoyFlex</span>
      </div>
      <h1>Login</h1>
      <p class="muted">Use your email and password to sign in.</p>

      <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST">
        <label>
          Email
          <input type="email" name="email" required />
        </label>

        <label>
          Password
          <input type="password" name="password" required />
        </label>

        <button type="submit">Login</button>
      </form>

      <p class="muted" style="margin-top: 12px;">
        No account? <a href="register.php" class="link">Register here</a>
      </p>
    </div>
  </div>
</body>
</html>
