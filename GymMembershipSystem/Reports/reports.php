<?php
require_once __DIR__ . '/../classes/report.php';

$report = new Reports();
$stats = $report->getStats();
$expiring = $report->getExpiringTodayList();
$expired = $report->getExpiredList();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports & Analytics - Gym Management System</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <h1 class="site-title">Reports & Analytics</h1>
            <div class="header-actions">
                <a href="../index.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="stats-grid" aria-labelledby="overview-heading">
            <h2 id="overview-heading" class="sr-only">Overview</h2>

            <div class="stat-card">
                <h3>Total Memberships</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['total_memberships']); ?></p>
            </div>

            <div class="stat-card">
                <h3>Active Memberships</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['active_memberships']); ?></p>
            </div>

            <div class="stat-card">
                <h3>Expiring Today</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['expiring_today']); ?></p>
            </div>

            <div class="stat-card">
                <h3>Expired Memberships</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['expired_memberships']); ?></p>
            </div>
        </section>


        <section class="tables mt-8">
            <h3>Expiring Today</h3>
            <?php if(!empty($expiring)): ?>
            <table class="data-table" role="table">
                <thead>
                    <tr>
                        <th>Membership #</th>
                        <th>Member ID</th>
                        <th>Member Name</th>
                        <th>Plan</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($expiring as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['membership_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No memberships expiring today.</p>
            <?php endif; ?>

            <h3 class="mt-8">Recently Expired</h3>
            <?php if(!empty($expired)): ?>
            <table class="data-table" role="table">
                <thead>
                    <tr>
                        <th>Membership #</th>
                        <th>Member ID</th>
                        <th>Member Name</th>
                        <th>Plan</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($expired as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['membership_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No recently expired memberships.</p>
            <?php endif; ?>
        </section>

    </main>

    <footer class="site-footer">
        <p>&copy; 2025 Gym Management System</p>
    </footer>
</body>
</html>
