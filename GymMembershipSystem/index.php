<?php
 
    // //resume session here to fetch session values
    // session_start();

    // // if user already logged in
    // if (isset($_SESSION['user']) && ($_SESSION['user'] == 'Staff' || $_SESSION['user'] == 'Admin')){
    //     // for now will send user to Homepage
    //     header('location: ../index.php');
    // }else{
    //     // if user is not log in, send them to login
    //     header('location: account/login.php');
    // }

    // Start session so we can check authentication and show profile info
    session_start();

    // If user is not logged in, send to landing page
    if (!isset($_SESSION['user'])) {
        header('Location: gymmembershipsystem.php');
        exit();
    }

    // Initialize Reports early for header notifications
    require_once __DIR__ . '/classes/report.php';
    $report = new Reports();
    $unreadCount = $report->getUnreadNotificationsCount();
    $allNotifications = $report->getAllNotifications(30, 0);

    // Also include transient (computed) notifications so alerts that are generated
    // on-the-fly (like expiry reminders and payments due) appear in the bell
    // without persisting duplicates to the database.
    $transientNotifs = [];
    try {
        $expiringTransient = $report->getExpiringIn3Days();
        foreach ($expiringTransient as $m) {
            $expiry_date = $m['expiry_date'] ?? null;
            $relative = '';
            if (!empty($expiry_date)) {
                $today = new DateTime(date('Y-m-d'));
                $exp = new DateTime(date('Y-m-d', strtotime($expiry_date)));
                $diff = (int)$today->diff($exp)->format('%r%a');
                if ($diff === 0) {
                    $relative = ' (today)';
                } elseif ($diff === 1) {
                    $relative = ' (tomorrow)';
                } elseif ($diff > 1) {
                    $relative = ' (in ' . $diff . ' days)';
                } elseif ($diff < 0) {
                    $relative = ' (expired)';
                }
            }

            $transientNotifs[] = [
                'notification_id' => null,
                'type' => 'expiry_reminder',
                'title' => 'Membership Expiring Soon',
                'message' => ($m['member_name'] ?? 'Member') . ' - Expires: ' . ($expiry_date ?? '') . $relative,
                'related_membership_id' => $m['membership_id'] ?? null,
                'related_payment_id' => null,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        $paymentsTransient = $report->getPaymentsDue();
        foreach ($paymentsTransient as $p) {
            $transientNotifs[] = [
                'notification_id' => null,
                'type' => 'payment_due',
                'title' => 'Payment Due',
                'message' => ($p['member_name'] ?? 'Member') . ' - Amount: Php ' . number_format((float)($p['amount'] ?? 0), 2),
                'related_membership_id' => $p['membership_id'] ?? null,
                'related_payment_id' => $p['payment_id'] ?? null,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
    } catch (Exception $e) {
        // If those methods fail for any reason, just ignore transient additions
        $transientNotifs = [];
    }

    if (!empty($transientNotifs)) {
        // Put transient notifications first so they're visible at top
        $allNotifications = array_merge($transientNotifs, $allNotifications);
        $unreadCount += count($transientNotifs);
    }

?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="view.css">
    <title>Gym Membership System - Dashboard</title>
</head>
<body>

    <header class="site-header">
        <div class="header-inner">
            <h1 class="site-title">Gym Membership System</h1>
            <div class="auth-buttons" style="display: flex; align-items: center; gap: 12px;">
                <!-- Notification Bell -->
                <div class="notification-bell" style="position: relative;">
                    <button id="notificationBellBtn" style="background: none; border: none; cursor: pointer; font-size: 24px; position: relative; padding: 0; color: #3F070B;">
                        🔔
                        <?php if ($unreadCount > 0): ?>
                        <span style="position: absolute; top: -8px; right: -8px; background: #710A14; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">
                            <?= min($unreadCount, 99) ?>
                        </span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" style="display: none; position: absolute; top: 40px; right: 0; background: white; border: 2px solid #710A14; border-radius: 6px; box-shadow: 0 4px 12px rgba(63, 7, 11, 0.15); width: 380px; max-height: 500px; overflow-y: auto; z-index: 1000;">
                        <div style="padding: 12px; border-bottom: 2px solid #710A14; font-weight: bold; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(90deg, #f5f5f5 0%, #fff 100%); color: #3F070B;">
                            <span>Notifications</span>
                            <div style="display: flex; gap: 8px;">
                                <a href="#" id="emailSettingsBtn" style="background: none; border: none; color: #710A14; cursor: pointer; font-size: 16px; text-decoration: none;" title="Email Notifications">✉️</a>
                                <button id="clearNotifBtn" style="background: none; border: none; color: #710A14; cursor: pointer; font-size: 12px; text-decoration: none;">Clear All</button>
                            </div>
                        </div>
                        <div id="notificationList">
                            <?php if (!empty($allNotifications)): ?>
                                <?php foreach ($allNotifications as $notif): ?>
                                <div class="notification-item" data-type="<?= htmlspecialchars($notif['type']) ?>" data-membership-id="<?= htmlspecialchars($notif['related_membership_id'] ?? '') ?>" data-payment-id="<?= htmlspecialchars($notif['related_payment_id'] ?? '') ?>" style="padding: 12px; border-bottom: 1px solid #f0f0f0; background: <?= $notif['is_read'] ? '#fff' : '#fff9f0' ?>; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='<?= $notif['is_read'] ? '#f5f5f5' : '#ffe5d5' ?>'; this.style.borderLeft='4px solid #710A14';" onmouseout="this.style.background='<?= $notif['is_read'] ? '#fff' : '#fff9f0' ?>'; this.style.borderLeft='4px solid transparent';" data-notification-id="<?= htmlspecialchars($notif['notification_id']) ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                        <div style="flex: 1;">
                                            <strong style="color: #3F070B;"><?= htmlspecialchars($notif['title']) ?></strong>
                                            <?php if ($notif['message']): ?>
                                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #666;"><?= htmlspecialchars(substr($notif['message'], 0, 100)) ?></p>
                                            <?php endif; ?>
                                            <small style="color: #999;"><?= date('M d, H:i', strtotime($notif['created_at'])) ?></small>
                                        </div>
                                        <?php if (!$notif['is_read']): ?>
                                        <span style="background: #710A14; width: 8px; height: 8px; border-radius: 50%; margin-left: 8px; margin-top: 2px;"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div style="padding: 20px; text-align: center; color: #999;">
                                No notifications
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!isset($_SESSION['user'])): ?>
                    <a class="btn btn-outline" href="./account/login.php">Log In</a>
                    <a class="btn btn-primary" href="./Admin/addAdmin.php">Register</a>
                <?php else: ?>
                    <a class="btn btn-outline" href="./account/logout.php">Log Out</a>
                    <a class="profile-indicator" href="Profile/viewProfile.php" title="Logged in as <?= htmlspecialchars($_SESSION['user']['email'] ?? ($_SESSION['user']['firstname'] ?? 'Account')) ?>">
                        <div class="profile-avatar"><?= htmlspecialchars(strtoupper(substr(($_SESSION['user']['firstname'] ?? ''),0,1) . substr(($_SESSION['user']['lastname'] ?? ''),0,1))) ?></div>
                        <div class="profile-name"><?= htmlspecialchars($_SESSION['user']['firstname'] ?? $_SESSION['user']['email'] ?? 'Account') ?></div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="header-content">
            <h2 class="lead">Welcome to the Gym Membership Dashboard</h2>
            <p class="subtitle">Manage profiles, memberships, payments, renewals, and roles efficiently.</p>
        </div>
    </header>

    <hr>

        <?php
            // small reports summary on homepage
            require_once __DIR__ . '/classes/report.php';
            $report = new Reports();
            $stats = $report->getStats();
            $expiring = $report->getExpiringTodayList();
            
            // Get notifications
            $expiringIn3Days = $report->getExpiringIn3Days();
            $paymentsDue = $report->getPaymentsDue();

            // Annotate and bucket expiring items for clearer UI wording
            $expiringToday = $expiring; // already fetched above
            $today = new DateTime(date('Y-m-d'));
            $tomorrowCount = 0;
            $in2to3Count = 0;
            $annotatedExpiring = [];
            foreach ($expiringIn3Days as $m) {
                $expDate = new DateTime(date('Y-m-d', strtotime($m['expiry_date'])));
                $diff = (int)$today->diff($expDate)->format('%r%a');
                if ($diff === 1) {
                    $tomorrowCount++;
                    $m['relative'] = ' (tomorrow)';
                } elseif ($diff > 1) {
                    $in2to3Count++;
                    $m['relative'] = ' (in ' . $diff . ' days)';
                } elseif ($diff === 0) {
                    $m['relative'] = ' (today)';
                } else {
                    $m['relative'] = '';
                }
                $annotatedExpiring[] = $m;
            }
        ?>

        <main class="container">
            <!-- System Notifications -->
            <?php if (!empty($expiringIn3Days) || !empty($paymentsDue)): ?>
            <section class="notifications" style="margin-bottom: 20px;">
                <?php if (!empty($expiringIn3Days) || !empty($expiring)): ?>
                <div class="alert alert-warning" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 14px; margin-bottom: 10px;">
                    <strong>⚠️ Expiry Reminders:</strong>
                    <?php if (!empty($expiring)): ?>
                        <?= count($expiring) ?> membership(s) expiring today
                    <?php else: ?>
                        <?php
                            if ($tomorrowCount > 0 && $in2to3Count == 0) {
                                echo $tomorrowCount . ' membership(s) expiring tomorrow';
                            } else {
                                $parts = [];
                                if ($tomorrowCount > 0) $parts[] = $tomorrowCount . ' tomorrow';
                                if ($in2to3Count > 0) $parts[] = $in2to3Count . ' in 2-3 days';
                                echo implode(' and ', $parts) . ' expiring';
                            }
                        ?>
                    <?php endif; ?>
                    <ul style="margin: 8px 0 0 20px; font-size: 14px;">
                        <?php foreach (array_slice($annotatedExpiring, 0, 5) as $member): ?>
                        <li><?= htmlspecialchars($member['member_name']) ?> - Expires: <?= htmlspecialchars($member['expiry_date']) . htmlspecialchars($member['relative'] ?? '') ?></li>
                        <?php endforeach; ?>
                        <?php if (count($annotatedExpiring) > 5): ?>
                        <li>...and <?= count($annotatedExpiring) - 5 ?> more</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($paymentsDue)): ?>
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; padding: 14px; margin-bottom: 10px;">
                    <strong>💳 Payments Due:</strong> <?= count($paymentsDue) ?> payment(s) pending or overdue
                    <ul style="margin: 8px 0 0 20px; font-size: 14px;">
                        <?php foreach (array_slice($paymentsDue, 0, 5) as $payment): ?>
                        <li><?= htmlspecialchars($payment['member_name']) ?> - Amount: Php <?= number_format((float)$payment['amount'], 2) ?> (<?= htmlspecialchars($payment['payment_status']) ?>)</li>
                        <?php endforeach; ?>
                        <?php if (count($paymentsDue) > 5): ?>
                        <li>...and <?= count($paymentsDue) - 5 ?> more</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <section class="stats-grid" aria-label="Quick analytics">
                <div class="stat-card">
                    <h3>Total Memberships</h3>
                    <p class="stat-value"><?php echo htmlspecialchars($stats['total_memberships']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Active</h3>
                    <p class="stat-value"><?php echo htmlspecialchars($stats['active_memberships']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Expiring Today</h3>
                    <p class="stat-value"><?php echo htmlspecialchars($stats['expiring_today']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Expired</h3>
                    <p class="stat-value"><?php echo htmlspecialchars($stats['expired_memberships']); ?></p>
                </div>
            </section>

            <div class="mt-8">
                <a class="btn btn-outline" href="./Reports/reports.php">View full reports</a>
            </div>

        </main>

        <hr>

    
    
    <section class="container">
        <h3 class="section-title">Quick Access Manager</h3>
        
        <div class="quick-actions">
            <a class="btn btn-accent" href="./Profile/viewProfile.php">Open Profiles</a>
            <a class="btn btn-accent" href="./MemPlan/viewPlan.php">Open Plans</a>
            <a class="btn btn-accent" href="./Membership/viewMembership.php">Open Memberships</a>
            <a class="btn btn-accent" href="./Payment/viewPayment.php">Open Payments</a>
            <a class="btn btn-accent" href="./Renewal/viewRenewal.php">Open Renewals</a>
            <a class="btn btn-accent" href="./Role/viewRole.php">Open Roles</a>
                <a class="btn btn-accent" href="./Mail/sendEmail.php">Send Email</a>
        </div>
    </section>
    

    
    
    <footer class="site-footer">
        <p>&copy; 2025 Gym Management System</p>
    </footer>

    <script>
        // Toggle notification dropdown
        document.getElementById('notificationBellBtn').addEventListener('click', function(){
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event){
            const bell = document.querySelector('.notification-bell');
            const dropdown = document.getElementById('notificationDropdown');
            if (!bell.contains(event.target) && dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            }
        });

        // Handle notification item clicks
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(){
                const type = this.getAttribute('data-type');
                const membershipId = this.getAttribute('data-membership-id');
                const paymentId = this.getAttribute('data-payment-id');

                if (type === 'expiry_reminder' && membershipId) {
                    // Navigate to membership view
                    window.location.href = 'Membership/viewMembership.php';
                } else if (type === 'payment_due' && paymentId) {
                    // Open payment receipt in new tab
                    window.open('Payment/receipt.php?payment_id=' + paymentId, '_blank');
                }
                // Mark as read
                markNotificationAsRead(this);
            });
        });

        // Mark notification as read (AJAX)
        function markNotificationAsRead(notifElement) {
            notifElement.style.background = '#fff';
            const unreadDot = notifElement.querySelector('[style*="background: #710A14"]');
            if (unreadDot) {
                unreadDot.style.display = 'none';
            }
        }

        // Email settings button
        document.getElementById('emailSettingsBtn').addEventListener('click', function(e){
            e.preventDefault();
            window.location.href = 'Settings/emailSettings.php';
        });

        // Clear all notifications
        document.getElementById('clearNotifBtn').addEventListener('click', function(e){
            e.preventDefault();
            if (confirm('Are you sure you want to clear all notifications?')) {
                alert('Notifications cleared!');
                // TODO: AJAX call to clear all notifications
            }
        });
    </script>
</body>
</html>