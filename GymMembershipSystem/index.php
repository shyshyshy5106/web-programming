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

?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Gym Membership System - Dashboard</title>
</head>
<body>

    <header class="site-header">
        <div class="header-inner">
            <h1 class="site-title">Gym Membership System</h1>
            <div class="auth-buttons">
                <a class="btn btn-outline" href="http://localhost/GymMembershipSystem/account/login.php">Log In</a>
                <a class="btn btn-outline" href="http://localhost/GymMembershipSystem/account/logout.php">Log Out</a>
                <a class="btn btn-primary" href="http://localhost/GymMembershipSystem/Admin/addAdmin.php">Register</a>
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
        ?>

        <main class="container">
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
                <a class="btn btn-outline" href="http://localhost/GymMembershipSystem/Reports/reports.php">View full reports</a>
            </div>

        </main>

        <hr>

    
    
    <section class="container">
        <h3 class="section-title">Quick Access Manager</h3>
        
        <div class="quick-actions">
            <a class="btn btn-accent" href="http://localhost/GymMembershipSystem/Profile/viewProfile.php">Open Profiles</a>
            <a class="btn btn-accent" href="http://localhost/GymMembershipSystem/MemPlan/viewPlan.php">Open Plans</a>
            <a class="btn btn-accent" href="http://localhost/GymMembershipSystem/Membership/viewMembership.php">Open Memberships</a>
            <a class="btn btn-accent" href="http://localhost/GymMembershipSystem/Payment/viewPayment.php">Open Payments</a>
            <a class="btn btn-accent" href="http://localhost/GymMembershipSystem/Renewal/viewRenewal.php">Open Renewals</a>
            <a class="btn btn-accent" href="http://localhost/GymMembershipSystem/Role/viewRole.php">Open Roles</a>
        </div>
    </section>
    

    
    
    <footer class="site-footer">
        <p>&copy; 2025 Gym Management System</p>
    </footer>
</body>
</html>