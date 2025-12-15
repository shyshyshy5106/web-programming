<?php
session_start();
// If user already logged in, send them to dashboard
if (isset($_SESSION['user'])) {
  header('Location: index.php');
  exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gym Membership System — Automate Memberships & Boost Retention</title>
  <meta name="description" content="Automate renewals and payments, reduce admin time, and keep more members with Gym Membership System. Schedule a free demo.">
  <link rel="stylesheet" href="landing.css">
</head>
<body>
  <header class="site-header">
    <div class="wrap">
      <div class="brand"><strong>Gym Membership System</strong></div>
      <nav class="nav">
        <a href="#features">Features</a>
        <a href="#benefits">Benefits</a>
        <a class="btn-outline" href="account/login.php">Log In</a>
        <a class="btn-primary" href="Admin/addAdmin.php">Sign Up</a>
      </nav>
    </div>
  </header>

  <main>
    <!-- Hero -->
    <section class="hero">
      <div class="wrap hero-grid">
        <div class="hero-copy">
          <h1>Automate Memberships. Cut Admin Time. Keep Members Longer.</h1>
          <p class="lead">All-in-one membership management built for busy gym owners — automate renewals, payments, reminders, and reporting. Ready to onboard in under a day.</p>
          <div class="hero-ctas">
            <a class="btn-primary" href="#demo">Schedule Your Free Demo</a>
            <a class="btn-secondary" href="#pricing">Start 14‑Day Trial</a>
          </div>
          <ul class="hero-features">
            <li><strong>Automate renewals:</strong> Reduce churn with timely reminders</li>
            <li><strong>Payments & receipts:</strong> Automatic billing & tracking</li>
            <li><strong>Reports:</strong> Revenue and retention insights</li>
          </ul>
        </div>
              </div>
    </section>

    <!-- Login and Sign Up are handled by dedicated pages -->

    <!-- Benefits -->
    <section id="benefits" class="wrap section">
      <h2>Why Gym Owners Love It</h2>
      <div class="cards">
        <div class="card">
          <h3>Save Hours Every Week</h3>
          <p>Automate renewals, receipts and reminders so staff can focus on members.</p>
        </div>
        <div class="card">
          <h3>Reduce Churn</h3>
          <p>Personalized reminders and renewal workflows keep members engaged.</p>
        </div>
        <div class="card">
          <h3>Get Paid Faster</h3>
          <p>Clear payment flows, retries, and receipts reduce failed payments.</p>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="wrap section alt">
      <h2>Key Features</h2>
      <div class="features-grid">
        <div class="feature"><strong>Member Profiles</strong><p>Full history, check-ins and notes in one place.</p></div>
        <div class="feature"><strong>Automated Billing</strong><p>Recurring payments, failures handling and receipts.</p></div>
        <div class="feature"><strong>Renewal Workflows</strong><p>Flexible reminders, grace periods and offers to save members.</p></div>
        <div class="feature"><strong>Emails & Logs</strong><p>Send templates, see delivery logs and audit history.</p></div>
        <div class="feature"><strong>Reports</strong><p>Revenue, churn, and new-member insights at a glance.</p></div>
        <div class="feature"><strong>Security</strong><p>Role-based access and safe daily backups.</p></div>
      </div>
    </section>

    <!-- Social Proof -->
    <section class="wrap section">
      <h2>Trusted by Dozens of Local Gyms</h2>
      <div class="testimonials">
        <blockquote>
          "We cut admin time by 70% and increased renewals by 12% — our coaches love it." <cite>— Jess, FitTown Gym</cite>
        </blockquote>
        <blockquote>
          "Automated billing saved us headaches. Setup was fast and the team is supportive." <cite>— Carlos, CoreFit Studio</cite>
        </blockquote>
      </div>
    </section>

  </main>

  <footer class="site-footer">
    <div class="wrap">
      <div class="footer-left">© <span id="year"></span> Gym Membership System</div>
      <div class="footer-right">
        <a href="#">Privacy</a>
        <a href="#">Terms</a>
        <a href="#">Support</a>
      </div>
    </div>
  </footer>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
    // Optional: Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(function(a){
      a.addEventListener('click', function(e){
        var target = a.getAttribute('href');
        if (target && target !== '#') {
          e.preventDefault();
          document.querySelector(target).scrollIntoView({behavior:'smooth'});
        }
      });
    });

    // Login/Sign Up use separate pages (no modals on the landing page)
  </script>
</body>
</html>