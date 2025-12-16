<?php

require_once 'database.php';

class Notification extends Database {

    /**
     * Log expiry reminder notification
     */
    public function logExpiryReminder($membership_id, $member_name, $expiry_date) {
        $title = "Membership Expiring Soon";
        $message = "{$member_name} - Expires on {$expiry_date}";
        $this->logNotification('expiry_reminder', $title, $message, $membership_id, null, 3);
    }

    /**
     * Log payment due notification
     */
    public function logPaymentDue($payment_id, $membership_id, $member_name, $amount) {
        $title = "Payment Due";
        $message = "{$member_name} - Amount: Php {$amount}";
        $this->logNotification('payment_due', $title, $message, $membership_id, $payment_id, 7);
    }

    /**
     * Generic notification logger
     */
    public function logNotification($type, $title, $message = '', $membership_id = null, $payment_id = null, $expires_in_days = null) {
        $conn = $this->connect();
        
        $sql = "INSERT INTO notifications (type, title, message, related_membership_id, related_payment_id, is_read, created_at";
        if ($expires_in_days) {
            $sql .= ", expires_at";
        }
        $sql .= ") VALUES (:type, :title, :message, :membership_id, :payment_id, 0, NOW()";
        if ($expires_in_days) {
            $sql .= ", DATE_ADD(NOW(), INTERVAL :expires_days DAY)";
        }
        $sql .= ")";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':membership_id', $membership_id);
        $stmt->bindParam(':payment_id', $payment_id);
        if ($expires_in_days) {
            $stmt->bindParam(':expires_days', (int)$expires_in_days, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }

    /**
     * Send email notification (placeholder for email system)
     */
    public function sendEmailNotification($email, $subject, $body) {
        // TODO: Implement email sending (use PHPMailer or similar)
        // For now, this is a placeholder
        $to = $email;
        $headers = "From: noreply@gymmembership.local\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Uncomment to enable email sending
        // return mail($to, $subject, $body, $headers);
        
        // For now, just log to file
        $logFile = __DIR__ . '/../logs/emails.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] To: $to, Subject: $subject\n", FILE_APPEND);
        return true;
    }

}

?>
