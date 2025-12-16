<?php

    require_once 'database.php';

    class Memberships extends Database{
        public $membership_id = "";
        public $member_id = "";  // References profile.id where role_id = 1
        public $plan_id = "";
        public $start_date = "";
        public $expiry_date = "";
        public $original_expiry_date = "";
        public $membership_status = "";
        public $employee_id = ""; // References profile.id where role_id = 2
        
        private function validateMemberProfile($profile_id) {
            if (empty($profile_id)) return false;
            $sql = "SELECT COUNT(*) as total FROM profile WHERE id = :id AND role_id = 1";
            $query = $this->connect()->prepare($sql);
            $pid = (int)$profile_id;
            $query->bindParam(":id", $pid, PDO::PARAM_INT);
            if($query->execute()) {
                $record = $query->fetch();
                return $record["total"] > 0;
            }
            return false;
        }

        private function validateStaffProfile($profile_id) {
            if (empty($profile_id)) return false;
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
        
        public function isMembershipExist($pmember_id, $pplan_id = "") 
        {
                // Validate member profile
                if (!$this->validateMemberProfile($pmember_id)) {
                    return false;
                }

                // ensure ids are integers to avoid accidental string comparison
                $pmember_id = (int)$pmember_id;
                $pplan_id = (int)$pplan_id;

                $sql = "SELECT COUNT(*) as total FROM membership WHERE member_id = :member_id AND plan_id = :plan_id";
                $query = $this->connect()->prepare($sql);
                $query->bindParam(":member_id", $pmember_id, PDO::PARAM_INT);
                $query->bindParam(":plan_id", $pplan_id, PDO::PARAM_INT);
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

        public function addMembership(){
            // Validate member profile (role_id = 1)
            if (!$this->validateMemberProfile($this->member_id)) {
                return false;
            }

            // Validate staff profile (role_id = 2)
            if (!$this->validateStaffProfile($this->employee_id)) {
                return false;
            }

            // cast foreign key ids to integers for safety
            $this->member_id = (int)$this->member_id;
            $this->plan_id = (int)$this->plan_id;
            $this->employee_id = ($this->employee_id === "" || $this->employee_id === null) ? null : (int)$this->employee_id;

            $sql = "INSERT INTO membership(member_id, plan_id, start_date, expiry_date, original_expiry_date, membership_status, employee_id) VALUES(:member_id, :plan_id, :start_date, :expiry_date, :original_expiry_date, :membership_status, :employee_id)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":member_id", $this->member_id, PDO::PARAM_INT);
            $query->bindParam(":plan_id", $this->plan_id, PDO::PARAM_INT);
            $query->bindParam(":start_date", $this->start_date);
            $query->bindParam(":expiry_date", $this->expiry_date);
            $query->bindParam(":original_expiry_date", $this->original_expiry_date);
            $query->bindParam(":membership_status", $this->membership_status);
            // bind employee_id as integer or null
            if ($this->employee_id === null) {
                $query->bindValue(":employee_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
            }
            return $query->execute();
        }

        public function fetchMembership($pmembership_id){
            $sql = "SELECT m.*, 
                          mp.fname as member_fname, mp.mname as member_mname, mp.lname as member_lname,
                          sp.fname as staff_fname, sp.mname as staff_mname, sp.lname as staff_lname,
                          p.plan_name
                   FROM membership m 
                   LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                   LEFT JOIN profile sp ON m.employee_id = sp.id AND sp.role_id = 2
                   LEFT JOIN membership_plans p ON m.plan_id = p.plan_id
                   WHERE m.membership_id=:membership_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":membership_id", $pmembership_id);
            if ($query->execute()){
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function editMembership($pmembership_id){
            // Validate member profile (role_id = 1)
            if (!$this->validateMemberProfile($this->member_id)) {
                return false;
            }

            // Validate staff profile (role_id = 2)
            if (!$this->validateStaffProfile($this->employee_id)) {
                return false;
            }

            // cast ids for safety
            $this->member_id = (int)$this->member_id;
            $this->plan_id = (int)$this->plan_id;
            $this->employee_id = ($this->employee_id === "" || $this->employee_id === null) ? null : (int)$this->employee_id;
            $pmembership_id = (int)$pmembership_id;

            $sql = "UPDATE membership SET member_id=:member_id, plan_id=:plan_id, start_date=:start_date, expiry_date=:expiry_date, original_expiry_date=:original_expiry_date, membership_status=:membership_status, employee_id=:employee_id WHERE membership_id=:membership_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":member_id", $this->member_id, PDO::PARAM_INT);
            $query->bindParam(":plan_id", $this->plan_id, PDO::PARAM_INT);
            $query->bindParam(":start_date", $this->start_date);
            $query->bindParam(":expiry_date", $this->expiry_date);
            $query->bindParam(":original_expiry_date", $this->original_expiry_date);
            $query->bindParam(":membership_status", $this->membership_status);
            if ($this->employee_id === null) {
                $query->bindValue(":employee_id", null, PDO::PARAM_NULL);
            } else {
                $query->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
            }
            $query->bindParam(":membership_id", $pmembership_id, PDO::PARAM_INT);
            return $query->execute();
        }

        public function removeMembership($pmembership_id){
            $sql = "DELETE FROM membership WHERE membership_id=:membership_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":membership_id", $pmembership_id);
            return $query->execute();
        }

        public function viewMembership($search = "", $status = ""){
            $sql = "SELECT m.*, 
                          mp.fname as member_fname, mp.mname as member_mname, mp.lname as member_lname,
                          sp.fname as staff_fname, sp.mname as staff_mname, sp.lname as staff_lname,
                          p.plan_name
                   FROM membership m 
                   LEFT JOIN profile mp ON m.member_id = mp.id AND mp.role_id = 1
                   LEFT JOIN profile sp ON m.employee_id = sp.id AND sp.role_id = 2
                   LEFT JOIN membership_plans p ON m.plan_id = p.plan_id
                   WHERE (CONCAT(mp.fname, ' ', mp.lname) LIKE CONCAT('%',:search,'%') 
                         OR m.member_id LIKE CONCAT('%',:search,'%'))
                   AND (";
            
            // Handle status filters with expiry date logic
            if ($status === "Expired") {
                // Show memberships where expiry_date < CURDATE() (considering renewals)
                $sql .= "m.expiry_date < CURDATE()";
            } else if ($status === "Active") {
                // Show only memberships that are Active status AND not expired (expiry_date >= CURDATE())
                $sql .= "m.membership_status = 'Active' AND m.expiry_date >= CURDATE()";
            } else if (!empty($status)) {
                // For other statuses (Freeze, Suspended), use the stored status
                $sql .= "m.membership_status LIKE CONCAT('%',:status,'%')";
            } else {
                // No status filter, show all
                $sql .= "1=1";
            }
            
            $sql .= ") ORDER BY m.membership_id ASC";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":search", $search);
            if (!empty($status) && $status !== "Expired" && $status !== "Active") {
                $query->bindParam(":status", $status);
            }

            if($query->execute()){
                return $query->fetchAll();
            } else {
                return null;
            }
        }
    }