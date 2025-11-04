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

}

?>