<?php
session_start();

// Check authorization - only Admin and Staff can view email logs
if (!isset($_SESSION["user"])) {
    header("Location: ../account/login.php");
    exit;
}

$user_role = $_SESSION["user"]["role"] ?? null;
if (!in_array($user_role, ['Admin', 'Staff'])) {
    $_SESSION['error_message'] = 'Unauthorized: Only Admin and Staff can view email logs.';
    header("Location: ../index.php");
    exit;
}

require_once '../classes/database.php';
require_once '../classes/email_service.php';

class EmailLogViewer extends Database {
    
    public function getEmailLogs($limit = 50, $offset = 0, $filters = []) {
        $conn = $this->connect();
        $sql = "SELECT * FROM email_logs WHERE 1=1";
        $params = [];

        // Apply filters
        if (!empty($filters['recipient'])) {
            $sql .= " AND recipient LIKE :recipient";
            $params['recipient'] = '%' . $filters['recipient'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindParam(':' . $key, $params[$key], PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getTotalEmailLogs($filters = []) {
        $conn = $this->connect();
        $sql = "SELECT COUNT(*) as total FROM email_logs WHERE 1=1";
        $params = [];

        if (!empty($filters['recipient'])) {
            $sql .= " AND recipient LIKE :recipient";
            $params['recipient'] = '%' . $filters['recipient'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        try {
            $stmt = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindParam(':' . $key, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}

$viewer = new EmailLogViewer();

// Get filter parameters
$recipient_filter = $_GET['recipient'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

$filters = [
    'recipient' => $recipient_filter,
    'status' => $status_filter,
    'date_from' => $date_from,
    'date_to' => $date_to
];

$logs = $viewer->getEmailLogs($limit, $offset, $filters);
$total = $viewer->getTotalEmailLogs($filters);
$total_pages = ceil($total / $limit);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Logs - Gym Membership System</title>
    <link rel="stylesheet" href="../index.css">
    <style>
        .logs-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .filter-section {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .filter-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 4px;
            color: #333;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
        }

        .filter-actions button,
        .filter-actions a {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-filter {
            background: #710A14;
            color: white;
        }

        .btn-filter:hover {
            background: #5a0a0f;
        }

        .btn-reset {
            background: #ddd;
            color: #333;
        }

        .btn-reset:hover {
            background: #ccc;
        }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }

        .logs-table th {
            background: #f5f5f5;
            border-bottom: 2px solid #ddd;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #333;
        }

        .logs-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .logs-table tbody tr:hover {
            background: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-sent-success {
            background: #d4edda;
            color: #155724;
        }

        .status-sent-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .status-test-mode {
            background: #d1ecf1;
            color: #0c5460;
        }

        .email-preview {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 13px;
            color: #666;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-decoration: none;
            color: #710A14;
            font-size: 13px;
        }

        .pagination a:hover {
            background: #f5f5f5;
        }

        .pagination .current {
            background: #710A14;
            color: white;
            border-color: #710A14;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .stats-bar {
            background: #f5f5f5;
            border-left: 4px solid #710A14;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .filter-group {
                grid-template-columns: 1fr;
            }

            .logs-table {
                font-size: 12px;
            }

            .logs-table th,
            .logs-table td {
                padding: 8px;
            }

            .email-preview {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="logs-container">
        <h1 class="site-title">Email Logs</h1>
        <p style="color: #666;">Audit trail of all emails sent to members</p>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <strong>Total Emails:</strong> <?php echo number_format($total); ?> 
            | <strong>Page:</strong> <?php echo $page; ?> of <?php echo $total_pages; ?>
            <?php if ($total > $limit): ?>
                | <strong>Showing:</strong> <?php echo min(($page - 1) * $limit + 1, $total); ?> - <?php echo min($page * $limit, $total); ?>
            <?php endif; ?>
        </div>

        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <div>
                        <label for="recipient">Recipient Email:</label>
                        <input type="text" id="recipient" name="recipient" value="<?php echo htmlspecialchars($recipient_filter); ?>" placeholder="Search by email...">
                    </div>
                    <div>
                        <label for="status">Status:</label>
                        <select id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="PENDING" <?php echo $status_filter === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                            <option value="SENT_SUCCESS" <?php echo $status_filter === 'SENT_SUCCESS' ? 'selected' : ''; ?>>Sent Success</option>
                            <option value="SENT_FAILED" <?php echo $status_filter === 'SENT_FAILED' ? 'selected' : ''; ?>>Sent Failed</option>
                            <option value="TEST_MODE_SUCCESS" <?php echo $status_filter === 'TEST_MODE_SUCCESS' ? 'selected' : ''; ?>>Test Mode</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_from">Date From:</label>
                        <input type="date" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div>
                        <label for="date_to">Date To:</label>
                        <input type="date" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">Apply Filters</button>
                    <a href="viewEmails.php" class="btn-reset">Clear Filters</a>
                </div>
            </form>
        </div>

        <!-- Email Logs Table -->
        <?php if (!empty($logs)): ?>
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>Sender</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['recipient']); ?></td>
                            <td><?php echo htmlspecialchars(substr($log['subject'], 0, 50)); ?></td>
                            <td><?php echo htmlspecialchars($log['sender_name'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower(str_replace('_', '-', $log['status'])); ?>">
                                    <?php echo htmlspecialchars($log['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                            <td>
                                <span class="email-preview" title="<?php echo htmlspecialchars($log['notes'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($log['notes'] ?? 'N/A', 0, 50)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="viewEmails.php?page=1<?php echo http_build_query(array_filter($filters)); ?>">« First</a>
                        <a href="viewEmails.php?page=<?php echo $page - 1; ?><?php echo http_build_query(array_filter($filters)); ?>">‹ Previous</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="viewEmails.php?page=<?php echo $i; ?><?php echo http_build_query(array_filter($filters)); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="viewEmails.php?page=<?php echo $page + 1; ?><?php echo http_build_query(array_filter($filters)); ?>">Next ›</a>
                        <a href="viewEmails.php?page=<?php echo $total_pages; ?><?php echo http_build_query(array_filter($filters)); ?>">Last »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <p>No email logs found matching your criteria.</p>
                <a href="viewEmails.php" style="color: #710A14; text-decoration: none;">Clear filters and try again</a>
            </div>
        <?php endif; ?>

        <div style="margin-top: 24px; text-align: center;">
            <a href="../index.php" style="color: #710A14; text-decoration: none;">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
