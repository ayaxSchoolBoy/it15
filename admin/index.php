<?php
require __DIR__ . '/auth.php';

// CSRF token for admin API calls
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin QR Scanner - Gym Membership</title>
  <link rel="stylesheet" href="/it15/admin/assets/styles.css?v=7" />
  <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken); ?>" />
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="brand">
        <span class="brand-mark"></span>
        <span class="brand-name">PinoyFlex</span>
      </div>
      <nav class="nav">
        <a class="nav-link" href="../admin_dashboard.php">Dashboard</a>
        <a class="nav-link" href="index.php">Scanner</a>
        <a class="nav-link" href="members.php">Members</a>
      </nav>
      <div class="actions">
        <span class="user-chip">Admin</span>
        <a class="btn outline" href="logout.php">Log out</a>
      </div>
    </header>

    <section class="hero">
      <div class="hero-text">
        <p class="eyebrow">Scanner Console</p>
        <h1>Fast <span>QR</span> Validation</h1>
        <p class="lead">Use the camera below to verify member access in real-time.</p>
        <div class="hero-actions">
          <a class="btn primary" href="#reader">Start Scanning</a>
          <a class="btn ghost" href="members.php">Manage Members</a>
        </div>
      </div>
      <div class="hero-card">
        <div class="hero-card-content">
          <p>Scanner Status</p>
          <h3>Ready to check-in</h3>
          <div class="tag">Secure Access</div>
        </div>
      </div>
    </section>

    <main class="content">
      <section class="scanner-card">
        <h2>Scan Membership QR Code</h2>
        <p class="hint">Allow camera access. Works on HTTPS or localhost.</p>

        <div class="toolbar">
          <select id="cameraSelect">
            <option value="">Loading cameras...</option>
          </select>
        </div>

        <div id="reader" class="reader"></div>

        <div class="controls">
          <button id="startBtn" type="button">Start Scanner</button>
          <button id="stopBtn" type="button" disabled>Stop Scanner</button>
        </div>

        <div class="status" id="scanStatus">Status: Waiting to start...</div>
      </section>

      <section class="result-card">
        <h2>Member Details</h2>
        <div id="result" class="result">
          <p class="placeholder">No scan yet.</p>
        </div>
      </section>

      <section class="history-card">
        <h2>Recent Scans</h2>
        <div id="history" class="history">
          <p class="placeholder">No history yet.</p>
        </div>
      </section>
    </main>

    <footer class="footer">
      <div>
        <div class="brand brand-footer">
          <span class="brand-mark"></span>
          <span class="brand-name">PinoyFlex</span>
        </div>
        <small>Camera access requires HTTPS or localhost.</small>
      </div>
      <div class="footer-actions">
        <a href="members.php" class="btn ghost">Members</a>
        <a href="logout.php" class="btn outline">Logout</a>
      </div>
    </footer>
  </div>

  <!-- QR Scanner Library (html5-qrcode) -->
  <script src="assets/html5-qrcode.min.js"></script>
  <script src="assets/scanner.js"></script>
</body>
</html>
