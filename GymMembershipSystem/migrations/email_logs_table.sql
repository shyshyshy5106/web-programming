-- Email Logs Table
-- Stores audit trail of all emails sent via the system

CREATE TABLE IF NOT EXISTS `email_logs` (
    `email_log_id` INT PRIMARY KEY AUTO_INCREMENT,
    `recipient` VARCHAR(255) NOT NULL COMMENT 'Email recipient address',
    `subject` VARCHAR(255) NOT NULL COMMENT 'Email subject line',
    `message_preview` LONGTEXT COMMENT 'First 200 characters of message body',
    `sender_email` VARCHAR(255) NOT NULL COMMENT 'Email sender address',
    `sender_name` VARCHAR(255) COMMENT 'Email sender display name',
    `status` ENUM('PENDING', 'SENT_SUCCESS', 'SENT_FAILED', 'TEST_MODE_SUCCESS') DEFAULT 'PENDING' COMMENT 'Delivery status',
    `notes` LONGTEXT COMMENT 'Retry attempts or error details',
    `sent_by_user_id` INT COMMENT 'User ID who initiated the email (if applicable)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Log created timestamp',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last status update',
    
    INDEX `idx_recipient` (`recipient`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_sender_email` (`sender_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
