<?php 

    require_once 'database.php';

    class Payments extends Database{
        public $payment_id = "";
        public $membership_id = "";
        public $payment_date = "";
        public $amount = "";
        public $payment_mode = "";
        public $payment_status = "";
        public $employee_id = "";

        private function validateStaffProfile($employee_id) {
            if ($employee_id === "" || $employee_id === null) {
                return false;
            }
            $sql = "SELECT COUNT(*) as total FROM profile WHERE id = :id AND role_id = 2";
            $query = $this->connect()->prepare($sql);
            $emp_id = (int)$employee_id;
            $query->bindParam(":id", $emp_id, PDO::PARAM_INT);
            if($query->execute()) {
                $record = $query->fetch();
                return $record["total"] > 0;
            }
            return false;
        }
        
        public function isPaymentExist($membership_id, $payment_date, $payment_id = "") {
            $membership_id = (int)$membership_id;
            $payment_id = (int)$payment_id;

            $sql = "SELECT COUNT(*) as total FROM payment WHERE membership_id = :membership_id AND payment_date = :payment_date AND payment_id <> :payment_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":membership_id", $membership_id, PDO::PARAM_INT);
            $query->bindParam(":payment_date", $payment_date);
            $query->bindParam(":payment_id", $payment_id, PDO::PARAM_INT);
        $record = NULL;
        if($query->execute()) {
            $record = $query->fetch();
        }

            if ($record["total"] > 0){
                return true;
            }else{
                return false;
            }
         }

        public function addPayment(){
            if (!$this->validateStaffProfile($this->employee_id)) {
                return false;
            }

            $this->membership_id = (int)$this->membership_id;
            $this->employee_id = ($this->employee_id === "" || $this->employee_id === null) ? null : (int)$this->employee_id;

            $sql = "INSERT INTO payment(membership_id, payment_date, amount, payment_mode, payment_status, employee_id) VALUES(:membership_id, :payment_date, :amount, :payment_mode, :payment_status, :employee_id)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":membership_id", $this->membership_id, PDO::PARAM_INT);
            $query->bindParam(":payment_date", $this->payment_date);
            $query->bindParam(":amount", $this->amount);
            $query->bindParam(":payment_mode", $this->payment_mode);
            $query->bindParam(":payment_status", $this->payment_status);
            if ($this->employee_id === null) {
                $query->bindValue(":employee_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
            }
            return $query->execute();
        }

        public function fetchPayment($ppayment_id){
            $sql = "SELECT * FROM payment WHERE payment_id=:payment_id";
            $query = $this->connect()->prepare($sql);
            $ppayment_id = (int)$ppayment_id;
            $query->bindParam(":payment_id", $ppayment_id, PDO::PARAM_INT);
            if ($query->execute()){
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function editPayment($ppayment_id){
            $this->membership_id = (int)$this->membership_id;
            $this->employee_id = ($this->employee_id === "" || $this->employee_id === null) ? null : (int)$this->employee_id;
            $ppayment_id = (int)$ppayment_id;

            $sql = "UPDATE payment SET membership_id=:membership_id, payment_date=:payment_date, amount=:amount, payment_mode=:payment_mode, payment_status=:payment_status, employee_id=:employee_id WHERE payment_id=:payment_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":membership_id", $this->membership_id, PDO::PARAM_INT);
            $query->bindParam(":payment_date", $this->payment_date);
            $query->bindParam(":amount", $this->amount);
            $query->bindParam(":payment_mode", $this->payment_mode);
            $query->bindParam(":payment_status", $this->payment_status);
            if ($this->employee_id === null) {
                $query->bindValue(":employee_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
            }
            $query->bindParam(":payment_id", $ppayment_id, PDO::PARAM_INT);
            return $query->execute();
        }

        public function removePayment($ppayment_id){
            $sql = "DELETE FROM payment WHERE payment_id=:payment_id";
            $query = $this->connect()->prepare($sql);
            $ppayment_id = (int)$ppayment_id;
            $query->bindParam(":payment_id", $ppayment_id, PDO::PARAM_INT);
            return $query->execute();
        }

        public function viewPayment($search = "", $status = ""){
            $sql = "SELECT p.*, 
                          CONCAT(pr.fname, ' ', pr.mname, ' ', pr.lname) as staff_name,
                          m.*, 
                          CONCAT(mp.fname, ' ', mp.mname, ' ', mp.lname) as member_name
                   FROM payment p
                   LEFT JOIN profile pr ON p.employee_id = pr.id AND pr.role_id = 2
                   LEFT JOIN membership m ON p.membership_id = m.membership_id
                   LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                   WHERE (CONCAT(mp.fname, ' ', mp.lname) LIKE CONCAT('%',:search,'%') 
                         OR p.membership_id LIKE CONCAT('%',:search,'%'))
                   AND p.payment_status LIKE CONCAT('%',:status,'%') 
                   ORDER BY p.payment_id ASC";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":search", $search);
            $query->bindParam(":status", $status);
 
            if($query->execute()){
                return $query->fetchAll();
            } else {
                return null;
            }
        }
    }