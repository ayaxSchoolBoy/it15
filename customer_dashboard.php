<?php
session_start();
$isCustomer = !empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'customer';
$name = htmlspecialchars($_SESSION['user_name'] ?? '');
$email = htmlspecialchars($_SESSION['user_email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Dashboard - Gym Membership</title>
  <link rel="stylesheet" href="/it15/assets/subscribe.css?v=6" />
</head>
<body class="dashboard" data-auth="<?php echo $isCustomer ? '1' : '0'; ?>">
  <div class="page">
    <header class="topbar">
      <div class="brand">
        <span class="brand-mark"></span>
        <span class="brand-name">PinoyFlex</span>
      </div>
      <nav class="nav">
        <a href="#" class="nav-link">Home</a>
        <a href="#subscribe" class="nav-link">Plans</a>
        <a href="#membership" class="nav-link">Membership</a>
        <a href="#contact" class="nav-link">Contact</a>
      </nav>
      <div class="actions">
        <?php if ($isCustomer): ?>
          <span class="user-chip">Welcome, <?php echo $name; ?></span>
          <a href="logout.php" class="btn outline">Log out</a>
        <?php else: ?>
          <a href="login.php" class="btn ghost">Log in</a>
          <a href="register.php" class="btn primary">Sign up</a>
        <?php endif; ?>
      </div>
    </header>

    <section class="hero">
      <div class="hero-text">
        <p class="eyebrow">Customer Dashboard</p>
        <h1>Fit to <span>Keep</span><br />Your Fitness</h1>
        <p class="lead">
          Manage your membership, track your QR access, and stay consistent with your
          training goals. Everything you need is organized here in one place.
        </p>
        <div class="hero-actions">
          <a href="#subscribe" class="btn primary">Get Started</a>
          <a href="#membership" class="btn ghost">View Membership</a>
        </div>
        <?php if ($isCustomer): ?>
          <div class="hero-meta">Signed in as <?php echo $email; ?></div>
        <?php else: ?>
          <div class="hero-meta">Sign in to activate your membership.</div>
        <?php endif; ?>
      </div>
      <div class="hero-card">
        <div class="hero-card-content">
          <p>Member Status</p>
          <h3>Ready for the next workout</h3>
          <div class="tag">Gym Access QR</div>
        </div>
      </div>
    </section>

    <section class="stats">
      <div class="stat">
        <h4>24/7</h4>
        <p>Gym Access</p>
      </div>
      <div class="stat">
        <h4>3 Plans</h4>
        <p>Flexible Options</p>
      </div>
      <div class="stat">
        <h4>Secure</h4>
        <p>QR Validation</p>
      </div>
    </section>

    <section class="dashboard-grid" id="subscribe">
      <div class="panel">
        <div class="panel-header">
          <h2>Subscribe Membership</h2>
          <p>Choose a plan that matches your routine.</p>
        </div>
        <form id="subscribeForm" class="form" <?php echo $isCustomer ? '' : 'aria-disabled="true"'; ?> >
          <input type="hidden" name="plan" id="planInput" value="Monthly" />

          <div class="plans">
            <button class="plan-card" type="button" data-plan="Monthly" <?php echo $isCustomer ? '' : 'disabled'; ?>>
              <div class="plan-header">
                <p>Essential Plan</p>
                <h3>$150<span>/Month</span></h3>
              </div>
              <ul>
                <li>20 Workouts</li>
                <li>Free showers and lockers</li>
                <li>Reliable trainer team</li>
                <li>5 days per week</li>
              </ul>
              <span class="plan-cta">Choose Monthly</span>
            </button>

            <button class="plan-card highlight" type="button" data-plan="Quarterly" <?php echo $isCustomer ? '' : 'disabled'; ?>>
              <div class="plan-header">
                <p>Essential Plan</p>
                <h3>$380<span>/Quarter</span></h3>
              </div>
              <ul>
                <li>Unlimited Workouts</li>
                <li>Priority class booking</li>
                <li>Reliable trainer team</li>
                <li>Nutrition program</li>
              </ul>
              <span class="plan-cta">Choose Quarterly</span>
            </button>

            <button class="plan-card" type="button" data-plan="Annual" <?php echo $isCustomer ? '' : 'disabled'; ?>>
              <div class="plan-header">
                <p>Essential Plan</p>
                <h3>$1200<span>/Year</span></h3>
              </div>
              <ul>
                <li>Unlimited Workouts</li>
                <li>Personal training consult</li>
                <li>Reliable trainer team</li>
                <li>VIP locker access</li>
              </ul>
              <span class="plan-cta">Choose Annual</span>
            </button>
          </div>

          <button type="submit" class="btn primary" <?php echo $isCustomer ? '' : 'disabled'; ?>>Subscribe Membership</button>
        </form>

        <div id="result" class="result">
          <?php if ($isCustomer): ?>
            <p class="placeholder">Your membership details will appear here.</p>
          <?php else: ?>
            <p class="placeholder">Please log in or sign up to subscribe.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="panel" id="membership">
        <div class="panel-header">
          <h2>My Membership</h2>
          <p>View your status, plan, and QR details.</p>
        </div>
        <div id="membershipDetails" class="result">
          <?php if ($isCustomer): ?>
            <p class="placeholder">Loading membership details...</p>
          <?php else: ?>
            <p class="placeholder">Log in to see your membership.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <section class="cta" id="contact">
      <div>
        <h2>Need help with your plan?</h2>
        <p>Our team is ready to assist you with upgrades or membership questions.</p>
      </div>
      <a class="btn primary" href="mailto:support@gymstar.local">Contact Us</a>
    </section>

    <footer class="footer">
      <div class="footer-grid">
        <div class="footer-col">
          <div class="brand brand-footer">
            <span class="brand-mark"></span>
            <span class="brand-name">PinoyFlex</span>
          </div>
          <p>
            Treadmills, bikes, and strength programs designed to boost endurance,
            burn calories, and keep you consistent.
          </p>
          <div class="footer-meta">
            <span class="footer-icon">🕒</span>
            <div>
              <strong>Opening hours</strong>
              <span>Monday – Friday 10 AM – 11 PM</span>
            </div>
          </div>
        </div>

        <div class="footer-col">
          <h4>Our Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#subscribe">Plans</a></li>
            <li><a href="#membership">Membership</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Contact Us</h4>
          <div class="footer-contact">
            <div class="footer-meta">
              <span class="footer-icon">📍</span>
              <div>
                <strong>Address Location</strong>
                <span>12 Street Rd Suite, Philippines</span>
              </div>
            </div>
            <div class="footer-meta">
              <span class="footer-icon">✉️</span>
              <div>
                <strong>Email Address</strong>
                <span>support@pinoyflex.local</span>
              </div>
            </div>
            <div class="footer-meta">
              <span class="footer-icon">📞</span>
              <div>
                <strong>Phone Number</strong>
                <span>+63 912 345 6789</span>
              </div>
            </div>
          </div>
        </div>

        <div class="footer-col">
          <h4>Our Circular</h4>
          <div class="footer-input">
            <input type="email" placeholder="Email Address..." />
            <button type="button" class="icon-btn">➤</button>
          </div>
          <div class="socials">
            <span class="social">f</span>
            <span class="social">x</span>
            <span class="social">in</span>
          </div>
        </div>
      </div>

      <div class="footer-actions">
        <a href="#subscribe" class="btn ghost">Back to Plans</a>
        <?php if ($isCustomer): ?>
          <a href="logout.php" class="btn outline">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn outline">Login</a>
        <?php endif; ?>
      </div>
    </footer>
  </div>

  <script src="assets/subscribe.js?v=3"></script>
  <script src="assets/customer.js"></script>
</body>
</html>
