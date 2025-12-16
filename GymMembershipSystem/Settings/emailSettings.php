<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user"])) {
    header('location: ../account/login.php');
    exit();
}

// Handle settings save
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // TODO: Save email preferences to database
    $message = "Email settings updated successfully!";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Notification Settings</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <h1 class="site-title">Email Notification Settings</h1>
            <div class="header-actions">
                <a href="../index.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>
        </div>
    </header>

    <main class="container mt-6">
        <?php if (!empty($message)): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; padding: 12px; margin-bottom: 16px; color: #155724;">
            ✓ <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <div style="background: white; padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h2 style="margin-top: 0;">Manage Email Notifications</h2>

            <form method="POST">
                <div style="margin-bottom: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="expiry_reminders" checked>
                        <strong>Expiry Reminders</strong>
                    </label>
                    <p style="color: #666; margin: 4px 0 0 28px; font-size: 14px;">Receive email alerts when memberships are expiring in 3 days</p>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="payment_due" checked>
                        <strong>Payment Due Alerts</strong>
                    </label>
                    <p style="color: #666; margin: 4px 0 0 28px; font-size: 14px;">Receive email alerts for pending or overdue payments</p>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="new_members" checked>
                        <strong>New Member Notifications</strong>
                    </label>
                    <p style="color: #666; margin: 4px 0 0 28px; font-size: 14px;">Receive email alerts for new member sign-ups</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="daily_summary" checked>
                        <strong>Daily Summary Email</strong>
                    </label>
                    <p style="color: #666; margin: 4px 0 0 28px; font-size: 14px;">Receive a daily summary of all system activity</p>
                </div>

                <hr style="margin: 20px 0;">

                <div style="margin-bottom: 16px;">
                    <label><strong>Email Frequency</strong></label>
                    <select name="frequency" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-top: 6px;">
                        <option value="immediate">Immediate</option>
                        <option value="daily">Daily Digest</option>
                        <option value="weekly">Weekly Digest</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label><strong>Your Email Address</strong></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_SESSION["user"]["email"] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-top: 6px;" required>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
                <a href="../index.php" class="btn btn-outline">Cancel</a>
            </form>
        </div>
    </main>

    <footer class="site-footer">
        <p>&copy; 2025 Gym Management System</p>
    </footer>
</body>
</html>
