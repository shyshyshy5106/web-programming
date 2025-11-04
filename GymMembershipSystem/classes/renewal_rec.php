<?php 

    require_once 'database.php';

    class RenewalRecords extends Database{
        public $renewal_id = "";
        public $membership_id = "";
        public $plan_id = "";
        public $renewal_date = "";
        public $previous_start_date = "";
        public $previous_expiry_date = "";
        public $new_start_date = "";
        public $new_expiry_date = "";
        public $payment_id = "";
        public $employee_id = "";

        private function validateStaffProfile($profile_id) {
            if ($profile_id === "" || $profile_id === null) {
                return false;
            }
            $sql = "SELECT COUNT(*) as total FROM profile WHERE id = :id AND role_id = 2";
            $query = $this->connect()->prepare($sql);
            $pid = (int)$profile_id;
            $query->bindParam(":id", $pid, PDO::PARAM_INT);
            if($query->execute()) {
                $record = $query->fetch();
                return $record["total"] > 0;
            }
            return false;
        }
       public function isRenewalExist($membership_id, $renewal_date, $renewal_id = "") {

        $membership_id = (int)$membership_id;
        $renewal_id = (int)$renewal_id;

        $sql = "SELECT COUNT(*) as total FROM renewal_record WHERE membership_id = :membership_id AND renewal_date = :renewal_date AND renewal_id <> :renewal_id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":membership_id", $membership_id, PDO::PARAM_INT);
        $query->bindParam(":renewal_date", $renewal_date);
        $query->bindParam(":renewal_id", $renewal_id, PDO::PARAM_INT);
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

        public function addRenewal(){
            // Validate staff profile (role_id = 2)
            if (!$this->validateStaffProfile($this->employee_id)) {
                return false;
            }

            $this->membership_id = (int)$this->membership_id;
            $this->plan_id = (int)$this->plan_id;
            $this->payment_id = ($this->payment_id === "" || $this->payment_id === null) ? null : (int)$this->payment_id;
            $this->employee_id = ($this->employee_id === "" || $this->employee_id === null) ? null : (int)$this->employee_id;

            $sql = "INSERT INTO renewal_record(membership_id, plan_id, renewal_date, previous_start_date, previous_expiry_date, new_start_date, new_expiry_date, payment_id, employee_id) VALUES(:membership_id, :plan_id, :renewal_date, :previous_start_date, :previous_expiry_date, :new_start_date, :new_expiry_date, :payment_id, :employee_id)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":membership_id", $this->membership_id, PDO::PARAM_INT);
            $query->bindParam(":plan_id", $this->plan_id, PDO::PARAM_INT);
            $query->bindParam(":renewal_date", $this->renewal_date);
            $query->bindParam(":previous_start_date", $this->previous_start_date);
            $query->bindParam(":previous_expiry_date", $this->previous_expiry_date);
            $query->bindParam(":new_start_date", $this->new_start_date);
            $query->bindParam(":new_expiry_date", $this->new_expiry_date);
            if ($this->payment_id === null) {
                $query->bindValue(":payment_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":payment_id", $this->payment_id, PDO::PARAM_INT);
            }
            if ($this->employee_id === null) {
                $query->bindValue(":employee_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
            }
            return $query->execute();
        }

        public function fetchRenewal($prenewal_id){
            $sql = "SELECT * FROM renewal_record WHERE renewal_id=:renewal_id";
            $query = $this->connect()->prepare($sql);
            $prenewal_id = (int)$prenewal_id;
            $query->bindParam(":renewal_id", $prenewal_id, PDO::PARAM_INT);
            if ($query->execute()){
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function editRenewal($prenewal_id){
 
            // Validate staff profile (role_id = 2)
            if (!$this->validateStaffProfile($this->employee_id)) {
                return false;
            }

            $this->membership_id = (int)$this->membership_id;
            $this->plan_id = (int)$this->plan_id;
            $this->payment_id = ($this->payment_id === "" || $this->payment_id === null) ? null : (int)$this->payment_id;
            $this->employee_id = ($this->employee_id === "" || $this->employee_id === null) ? null : (int)$this->employee_id;
            $prenewal_id = (int)$prenewal_id;

            $sql = "UPDATE renewal_record SET membership_id=:membership_id, plan_id=:plan_id, renewal_date=:renewal_date, previous_start_date=:previous_start_date, previous_expiry_date=:previous_expiry_date, new_start_date=:new_start_date, new_expiry_date=:new_expiry_date, payment_id=:payment_id, employee_id=:employee_id WHERE renewal_id=:renewal_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":membership_id", $this->membership_id, PDO::PARAM_INT);
            $query->bindParam(":plan_id", $this->plan_id, PDO::PARAM_INT);
            $query->bindParam(":renewal_date", $this->renewal_date);
            $query->bindParam(":previous_start_date", $this->previous_start_date);
            $query->bindParam(":previous_expiry_date", $this->previous_expiry_date);
            $query->bindParam(":new_start_date", $this->new_start_date);
            $query->bindParam(":new_expiry_date", $this->new_expiry_date);
            if ($this->payment_id === null) {
                $query->bindValue(":payment_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":payment_id", $this->payment_id, PDO::PARAM_INT);
            }
            if ($this->employee_id === null) {
                $query->bindValue(":employee_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
            }
            $query->bindParam(":renewal_id", $prenewal_id, PDO::PARAM_INT);
            return $query->execute();
        }

        public function removeRenewal($prenewal_id){
            $sql = "DELETE FROM renewal_record WHERE renewal_id=:renewal_id";
            $query = $this->connect()->prepare($sql);
            $prenewal_id = (int)$prenewal_id;
            $query->bindParam(":renewal_id", $prenewal_id, PDO::PARAM_INT);
            return $query->execute();
        }

        public function viewRenewal($search = ""){
            $sql = "SELECT r.*, pr.fname as staff_fname, pr.mname as staff_mname, pr.lname as staff_lname
                    FROM renewal_record r
                    LEFT JOIN profile pr ON r.employee_id = pr.id AND pr.role_id = 2
                    WHERE r.membership_id LIKE CONCAT('%',:search,'%') ORDER BY r.renewal_id ASC";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":search", $search);

            if($query->execute()){
                return $query->fetchAll();
            } else {
                return null;
            }
        }
    }
