<?php
session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Gym Membership</title>
  <link rel="stylesheet" href="/it15/admin/assets/styles.css?v=7" />
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="brand">
        <span class="brand-mark"></span>
        <span class="brand-name">PinoyFlex</span>
      </div>
      <nav class="nav">
        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
        <a class="nav-link" href="admin/index.php">Scanner</a>
        <a class="nav-link" href="admin/members.php">Members</a>
      </nav>
      <div class="actions">
        <span class="user-chip">Admin</span>
        <a href="logout.php" class="btn outline">Log out</a>
      </div>
    </header>

    <section class="hero">
      <div class="hero-text">
        <p class="eyebrow">Admin Control</p>
        <h1>Manage <span>Members</span> & Access</h1>
        <p class="lead">
          Review active memberships, validate QR entries, and keep your gym access secure.
        </p>
        <div class="hero-actions">
          <a href="admin/index.php" class="btn primary">Open QR Scanner</a>
          <a href="admin/members.php" class="btn ghost">Manage Members</a>
        </div>
      </div>
      <div class="hero-card">
        <div class="hero-card-content">
          <p>Today's Focus</p>
          <h3>Fast check-ins</h3>
          <div class="tag">Real-time Validation</div>
        </div>
      </div>
    </section>

    <main class="dashboard-grid">
      <a class="action-card" href="admin/index.php">
        <h2>QR Scanner</h2>
        <p>Validate member access quickly at the door.</p>
        <span class="action-link">Open Scanner</span>
      </a>
      <a class="action-card" href="admin/members.php">
        <h2>Manage Members</h2>
        <p>Activate, deactivate, and review memberships.</p>
        <span class="action-link">Go to Members</span>
      </a>
    </main>

    <footer class="footer">
      <div>
        <div class="brand brand-footer">
          <span class="brand-mark"></span>
          <span class="brand-name">PinoyFlex</span>
        </div>
        <p>Admin portal for gym access and members.</p>
      </div>
      <div class="footer-actions">
        <a href="admin/index.php" class="btn ghost">Scanner</a>
        <a href="logout.php" class="btn outline">Logout</a>
      </div>
    </footer>
  </div>
</body>
</html>
