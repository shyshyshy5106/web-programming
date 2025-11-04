<?php

    require_once 'database.php';

    class MembershipPlans extends Database{
       public $plan_id = "";
       public $plan_name = "";
       public $description = "";
       public $duration = "";
       public $price = "";
       public $plan_type = "";
       public $status = "";

         public function isPlanExist($pplan_name,$pplan_id=""){
            $sql = "SELECT COUNT(*) as total FROM membership_plans WHERE plan_name = :plan_name AND plan_id<>:plan_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":plan_name",$pplan_name);
            $query->bindParam(":plan_id",$pplan_id);
            $record = NULL;
            if($query->execute()){
                $record = $query->fetch();
            }

            if ($record["total"] > 0){
                return true;
            }else{
                return false;
            }
         }

        public function fetchPlan($pplan_id){
        $sql = "SELECT * FROM membership_plans WHERE plan_id=:plan_id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":plan_id", $pplan_id);
        if ($query->execute()){
            return $query->fetch();
        } else {
            return null;
        }
        }

        public function editPlan($pplan_id){
        $sql = "UPDATE membership_plans SET plan_name=:plan_name, description=:description, duration=:duration, price=:price, plan_type=:plan_type, status=:status WHERE plan_id=:plan_id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":plan_name", $this->plan_name);
        $query->bindParam(":description", $this->description);
        $query->bindParam(":duration", $this->duration);
        $query->bindParam(":price", $this->price);
        $query->bindParam(":plan_type", $this->plan_type);
        $query->bindParam(":plan_id", $pplan_id);
        $query->bindParam(":status", $this->status);
        return $query->execute();
        }

        public function removePlan($pplan_id){
        $sql = "DELETE FROM membership_plans WHERE plan_id=:plan_id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam("plan_id",$pplan_id);
        return $query->execute();
        }

        public function addPlan(){
            if ($this->isPlanExist($this->plan_name)) {
            return false;
        } 
        $sql = "INSERT INTO membership_plans(plan_name, description, duration, price, plan_type, status) VALUES(:plan_name, :description, :duration, :price, :plan_type, :status)";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":plan_name", $this->plan_name);
        $query->bindParam(":description", $this->description);
        $query->bindParam(":duration", $this->duration);
        $query->bindParam(":price", $this->price);
        $query->bindParam(":plan_type", $this->plan_type);
        $query->bindParam(":status", $this->status);
        return $query->execute();
        }

        public function viewPlan($search = "", $plan_type = ""){
        $sql = "SELECT * FROM membership_plans WHERE plan_name LIKE CONCAT('%',:search,'%') AND plan_type LIKE CONCAT('%',:plan_type,'%') ORDER BY plan_id  ASC";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":search", $search);
        $query->bindParam(":plan_type", $plan_type);

        if($query->execute()){
            return $query->fetchAll();
        } else {
            return null;
        }
        }
        
    }