<?php
/**
 * Database Setup/Migration Admin Tool
 * Initializes required database tables for new features
 * Only accessible from localhost or with proper authentication
 */

session_start();

// Security: Check if running from localhost or authenticated admin
$is_localhost = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === 'localhost' || $_SERVER['REMOTE_ADDR'] === '::1');
$is_authenticated_admin = isset($_SESSION["user"]) && $_SESSION["user"]["role"] === 'Admin';

if (!$is_localhost && !$is_authenticated_admin) {
    http_response_code(403);
    die('Unauthorized: This page is only accessible from localhost or as an authenticated Admin.');
}

require_once __DIR__ . '/../classes/database.php';

class DatabaseSetup extends Database {
    
    public function setupEmailLogsTable() {
        $conn = $this->connect();
        $sql = "CREATE TABLE IF NOT EXISTS `email_logs` (
                    `email_log_id` INT PRIMARY KEY AUTO_INCREMENT,
                    `recipient` VARCHAR(255) NOT NULL,
                    `subject` VARCHAR(255) NOT NULL,
                    `message_preview` LONGTEXT,
                    `sender_email` VARCHAR(255) NOT NULL,
                    `sender_name` VARCHAR(255),
                    `status` ENUM('PENDING', 'SENT_SUCCESS', 'SENT_FAILED', 'TEST_MODE_SUCCESS') DEFAULT 'PENDING',
                    `notes` LONGTEXT,
                    `sent_by_user_id` INT,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    
                    INDEX `idx_recipient` (`recipient`),
                    INDEX `idx_status` (`status`),
                    INDEX `idx_created_at` (`created_at`),
                    INDEX `idx_sender_email` (`sender_email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $conn->exec($sql);
            return ['success' => true, 'message' => 'email_logs table created successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error creating table: ' . $e->getMessage()];
        }
    }

    public function checkEmailLogsTable() {
        $conn = $this->connect();
        $sql = "SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_logs'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}

$setup = new DatabaseSetup();
$email_table_exists = $setup->checkEmailLogsTable();
$setup_result = null;

// Handle setup request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'setup_email_logs') {
        $setup_result = $setup->setupEmailLogsTable();
        // Refresh table check
        $email_table_exists = $setup->checkEmailLogsTable();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Gym Membership System</title>
    <link rel="stylesheet" href="../index.css">
    <style>
        .setup-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
        }

        .setup-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .setup-item {
            margin-bottom: 24px;
            padding: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fafafa;
        }

        .setup-item h3 {
            margin: 0 0 12px 0;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 12px;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #710A14;
            color: white;
        }

        .btn-primary:hover {
            background: #5a0a0f;
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .info-text {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <h1 style="margin-top: 0; color: #3F070B;">Database Setup</h1>
            <p style="color: #666; margin-bottom: 20px;">Initialize required database tables for the Gym Membership System features.</p>

            <?php if ($setup_result !== null): ?>
                <div class="alert <?php echo $setup_result['success'] ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo htmlspecialchars($setup_result['message']); ?>
                </div>
            <?php endif; ?>

            <div class="setup-item">
                <h3>Email Logs Table</h3>
                <p>Stores audit trail for all emails sent to members.</p>
                <div>
                    <span class="status-badge <?php echo $email_table_exists ? 'status-success' : 'status-pending'; ?>">
                        <?php echo $email_table_exists ? '✓ Created' : '⚠ Not Created'; ?>
                    </span>
                </div>
                <p class="info-text">
                    This table logs all email send attempts, delivery status, sender information, and timestamps for auditing purposes.
                </p>
                <?php if (!$email_table_exists): ?>
                    <form method="POST" style="margin-top: 12px;">
                        <input type="hidden" name="action" value="setup_email_logs">
                        <button type="submit" class="btn btn-primary">Create Table</button>
                    </form>
                <?php endif; ?>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: #f0f0f0; border-radius: 6px;">
                <h3 style="margin-top: 0;">Setup Complete!</h3>
                <p>All required tables have been created. You can now:</p>
                <ul style="margin: 8px 0; padding-left: 20px;">
                    <li><a href="../Mail/sendEmail.php">Send emails to members</a></li>
                    <li><a href="../index.php">Return to dashboard</a></li>
                </ul>
            </div>

            <div style="margin-top: 24px; text-align: center;">
                <a href="../index.php" style="color: #710A14; text-decoration: none;">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
