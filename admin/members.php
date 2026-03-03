<?php
require __DIR__ . '/auth.php';

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
  <title>Members - Gym Membership</title>
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
        <p class="eyebrow">Membership Control</p>
        <h1>Track <span>Subscriptions</span></h1>
        <p class="lead">Search, review, and update member access with confidence.</p>
        <div class="hero-actions">
          <a class="btn primary" href="#membersTable">View Members</a>
          <a class="btn ghost" href="index.php">Open Scanner</a>
        </div>
      </div>
      <div class="hero-card">
        <div class="hero-card-content">
          <p>Member Insights</p>
          <h3>Keep access up to date</h3>
          <div class="tag">Active Plans</div>
        </div>
      </div>
    </section>

    <main class="content">
      <section class="result-card">
        <div class="toolbar">
          <input id="searchInput" type="text" placeholder="Search by name or code" />
          <button id="searchBtn" type="button">Search</button>
        </div>

        <div class="table-wrap">
          <table class="table" id="membersTable">
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Plan</th>
                <th>Expiration</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="6" class="placeholder">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <footer class="footer">
      <div>
        <div class="brand brand-footer">
          <span class="brand-mark"></span>
          <span class="brand-name">PinoyFlex</span>
        </div>
        <p>Admin portal for memberships and access control.</p>
      </div>
      <div class="footer-actions">
        <a href="index.php" class="btn ghost">Scanner</a>
        <a href="logout.php" class="btn outline">Logout</a>
      </div>
    </footer>
  </div>

  <script src="assets/members.js"></script>
</body>
</html>
