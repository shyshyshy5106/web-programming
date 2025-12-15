-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL COMMENT 'expiry_reminder, payment_due, system',
    title VARCHAR(255) NOT NULL,
    message TEXT,
    related_membership_id INT,
    related_payment_id INT,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    INDEX idx_created (created_at),
    INDEX idx_read (is_read)
);
