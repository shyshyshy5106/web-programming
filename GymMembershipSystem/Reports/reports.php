<?php
require_once __DIR__ . '/../classes/report.php';

$report = new Reports();
$stats = $report->getStats();
$expiring = $report->getExpiringTodayList();
$expired = $report->getExpiredList();
$inactive = $report->getInactiveMembersList();
$newMembers = $report->getNewMembers(30); // last 30 days
$recentPayments = $report->getRecentPayments(200);

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

            <div class="stat-card">
                <h3>New Sign-ups Today</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['new_signups_today']); ?></p>
            </div>

            <div class="stat-card">
                <h3>New Sign-ups This Month</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['new_signups_month']); ?></p>
            </div>

            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p class="stat-value">Php <?php echo number_format($stats['total_revenue'], 2); ?></p>
            </div>

            <div class="stat-card">
                <h3>Revenue Today</h3>
                <p class="stat-value">Php <?php echo number_format($stats['revenue_today'], 2); ?></p>
            </div>
        </section>


        <section class="tables mt-8">
            <h3>Expiring Today</h3>
            <?php if(!empty($expiring)): ?>
            <div class="table-container">
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
                        <td class="truncate" data-fulltext="<?= htmlspecialchars($row['member_name']) ?>"><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
                <div class="table-empty">No memberships expiring today.</div>
            <?php endif; ?>

            <h3 class="mt-8">Recently Expired</h3>
            <?php if(!empty($expired)): ?>
            <div class="table-container">
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
                        <td class="truncate" data-fulltext="<?= htmlspecialchars($row['member_name']) ?>"><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
                <div class="table-empty">No recently expired memberships.</div>
            <?php endif; ?>
        </section>

        <section class="tables mt-8">
            <h3>Inactive Members</h3>
            <?php if(!empty($inactive)): ?>
            <div class="table-container">
            <table class="data-table" role="table">
                <thead>
                    <tr>
                        <th>Membership #</th>
                        <th>Member ID</th>
                        <th>Member Name</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($inactive as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['membership_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                        <td class="truncate" data-fulltext="<?= htmlspecialchars($row['member_name']) ?>"><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['membership_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
                <div class="table-empty">No inactive members found.</div>
            <?php endif; ?>
        </section>

        <section class="tables mt-8">
            <h3>New Members (Last 30 days)</h3>
            <?php if(!empty($newMembers)): ?>
            <div class="table-container">
            <table class="data-table" role="table">
                <thead>
                    <tr>
                        <th>Membership #</th>
                        <th>Member ID</th>
                        <th>Member Name</th>
                        <th>Plan</th>
                        <th>Start Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($newMembers as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['membership_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                        <td class="truncate" data-fulltext="<?= htmlspecialchars($row['member_name']) ?>"><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
                <div class="table-empty">No new members in the last 30 days.</div>
            <?php endif; ?>
        </section>

        <section class="tables mt-8">
            <h3>Recent Payment History</h3>
            <?php if(!empty($recentPayments)): ?>
            <div class="table-container">
            <table class="data-table" role="table">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Membership #</th>
                        <th>Member Name</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($recentPayments as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['membership_id']); ?></td>
                        <td class="truncate" data-fulltext="<?= htmlspecialchars($row['member_name']) ?>"><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td>Php <?php echo number_format((float)$row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_mode']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
                <div class="table-empty">No payment history available.</div>
            <?php endif; ?>
        </section>

    </main>

    <footer class="site-footer">
        <p>&copy; 2025 Gym Management System</p>
    </footer>
    <script src="../scripts/table-utils.js"></script>
</body>
</html>
