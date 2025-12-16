<?php

require_once 'database.php';

class Reports extends Database {

    // Returns associative stats
    public function getStats(){
        $conn = $this->connect();

        $stats = [];

        $sql = "SELECT COUNT(*) FROM membership";
        $stats['total_memberships'] = (int)$conn->query($sql)->fetchColumn();

        $sql = "SELECT COUNT(*) FROM membership WHERE expiry_date >= CURDATE()";
        $stats['active_memberships'] = (int)$conn->query($sql)->fetchColumn();

        $sql = "SELECT COUNT(*) FROM membership WHERE expiry_date = CURDATE()";
        $stats['expiring_today'] = (int)$conn->query($sql)->fetchColumn();

        $sql = "SELECT COUNT(*) FROM membership WHERE expiry_date < CURDATE()";
        $stats['expired_memberships'] = (int)$conn->query($sql)->fetchColumn();

        // New sign-ups
        $sql = "SELECT COUNT(*) FROM membership WHERE DATE(start_date) = CURDATE()";
        $stats['new_signups_today'] = (int)$conn->query($sql)->fetchColumn();

        $sql = "SELECT COUNT(*) FROM membership WHERE MONTH(start_date) = MONTH(CURDATE()) AND YEAR(start_date) = YEAR(CURDATE())";
        $stats['new_signups_month'] = (int)$conn->query($sql)->fetchColumn();

        // Total revenue from payments (sum of amounts)
        $sql = "SELECT COALESCE(SUM(amount),0) FROM payment";
        // fetchColumn returns string/NULL so cast to float
        $stats['total_revenue'] = (float)$conn->query($sql)->fetchColumn();

        // Revenue today
        $sql = "SELECT COALESCE(SUM(amount),0) FROM payment WHERE DATE(payment_date) = CURDATE()";
        $stats['revenue_today'] = (float)$conn->query($sql)->fetchColumn();

        // by plan
        $sql = "SELECT p.plan_name, COUNT(*) as total FROM membership m JOIN membership_plans p ON m.plan_id = p.plan_id GROUP BY p.plan_name ORDER BY total DESC";
        $stmt = $conn->query($sql);
        $stats['by_plan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    public function getExpiringTodayList(){
        $conn = $this->connect();
        $sql = "SELECT m.membership_id, m.member_id, CONCAT(mp.fname,' ',mp.lname) as member_name, m.expiry_date, p.plan_name
                FROM membership m
                LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                LEFT JOIN membership_plans p ON m.plan_id = p.plan_id
                WHERE m.expiry_date = CURDATE()
                ORDER BY m.expiry_date ASC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExpiredList(){
        $conn = $this->connect();
        $sql = "SELECT m.membership_id, m.member_id, CONCAT(mp.fname,' ',mp.lname) as member_name, m.expiry_date, p.plan_name
                FROM membership m
                LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                LEFT JOIN membership_plans p ON m.plan_id = p.plan_id
                WHERE m.expiry_date < CURDATE()
                ORDER BY m.expiry_date DESC LIMIT 200";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // List inactive members (membership_status = 'inactive')
    public function getInactiveMembersList($limit = 200){
        $conn = $this->connect();
        $sql = "SELECT m.membership_id, m.member_id, CONCAT(mp.fname,' ',mp.lname) as member_name, p.plan_name, m.membership_status, m.expiry_date
                FROM membership m
                LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                LEFT JOIN membership_plans p ON m.plan_id = p.plan_id
                WHERE m.membership_status = 'inactive'
                ORDER BY m.expiry_date DESC LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $limit = (int)$limit;
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        if($stmt->execute()){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    // List new members within the last N days (default 30)
    public function getNewMembers($days = 30){
        $conn = $this->connect();
        $sql = "SELECT m.membership_id, m.member_id, CONCAT(mp.fname,' ',mp.lname) as member_name, p.plan_name, m.start_date
                FROM membership m
                LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                LEFT JOIN membership_plans p ON m.plan_id = p.plan_id
                WHERE m.start_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                ORDER BY m.start_date DESC";
        $stmt = $conn->prepare($sql);
        $d = (int)$days;
        $stmt->bindParam(':days', $d, PDO::PARAM_INT);
        if($stmt->execute()){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    // Recent payment history
    public function getRecentPayments($limit = 200){
        $conn = $this->connect();
        $sql = "SELECT p.payment_id, p.membership_id, p.payment_date, p.amount, p.payment_mode, p.payment_status, CONCAT(mp.fname,' ',mp.lname) as member_name
                FROM payment p
                LEFT JOIN membership m ON p.membership_id = m.membership_id
                LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                ORDER BY p.payment_date DESC LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $l = (int)$limit;
        $stmt->bindValue(':limit', $l, PDO::PARAM_INT);
        if($stmt->execute()){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * Get memberships expiring in the next 3 days (reminders)
     */
    public function getExpiringIn3Days(){
        $conn = $this->connect();
        $sql = "SELECT m.membership_id, m.member_id, CONCAT(mp.fname,' ',mp.lname) as member_name, m.expiry_date, p.plan_name
                FROM membership m
                LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                LEFT JOIN membership_plans p ON m.plan_id = p.plan_id
                WHERE m.expiry_date > CURDATE() AND m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                ORDER BY m.expiry_date ASC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get overdue/pending payments (payments not yet completed or pending)
     */
    public function getPaymentsDue(){
        $conn = $this->connect();
        $sql = "SELECT p.payment_id, p.membership_id, CONCAT(mp.fname,' ',mp.lname) as member_name, p.amount, p.payment_status, p.payment_date
                FROM payment p
                LEFT JOIN membership m ON p.membership_id = m.membership_id
                LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                WHERE p.payment_status IN ('Pending', 'Incomplete') OR (p.payment_status = 'Pending' AND DATE(p.payment_date) < CURDATE())
                ORDER BY p.payment_date ASC LIMIT 10";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all notifications (both read and unread) with limit and pagination
     */
    public function getAllNotifications($limit = 50, $offset = 0){
        try {
            $conn = $this->connect();
            $sql = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            if($stmt->execute()){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            // Table doesn't exist yet
        }
        return [];
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCount(){
        try {
            $conn = $this->connect();
            $sql = "SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND (expires_at IS NULL OR expires_at > NOW())";
            return (int)$conn->query($sql)->fetchColumn();
        } catch (Exception $e) {
            // Table doesn't exist yet, return 0
            return 0;
        }
    }

    /**
     * Get unread notifications (latest first)
     */
    public function getUnreadNotifications($limit = 20){
        try {
            $conn = $this->connect();
            $sql = "SELECT * FROM notifications WHERE is_read = 0 AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY created_at DESC LIMIT :limit";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            if($stmt->execute()){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            // Table doesn't exist yet
        }
        return [];
    }

    /**
     * Add a new notification
     */
    public function addNotification($type, $title, $message = '', $membership_id = null, $payment_id = null, $expires_in_days = null){
        try {
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
        } catch (Exception $e) {
            // Table doesn't exist yet
            return false;
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($notification_id){
        try {
            $conn = $this->connect();
            $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', (int)$notification_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            // Table doesn't exist yet
            return false;
        }
    }

}

?>