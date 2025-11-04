<?php

    require_once 'database.php';

    class Profiles extends Database{
        public $id = "";
        public $role_id = "";
        public $fname = "";
        public $mname = "";
        public $lname = "";
        public $phone_num = "";
        public $address = "";
        public $sex = "";
        public $dob = "";
        public $join_date = "";
        public $status = "";
        public $created_at = "";
        public $updated_at = "";

        public function isProfileExist($pfname, $plname, $role_id, $pid = ""){
            $role_id = (int)$role_id;
            $id = (int)$pid;

            $sql = "SELECT COUNT(*) as total FROM profile WHERE fname = :fname AND lname = :lname AND role_id = :role_id";
            if ($id > 0) {
                $sql .= " AND id != :id";
            }
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":fname", $pfname);
            $query->bindParam(":lname", $plname);
            $query->bindParam(":role_id", $role_id, PDO::PARAM_INT);
            if ($id > 0) {
                $query->bindParam(":id", $id, PDO::PARAM_INT);
            }
            
            if($query->execute()){
                $record = $query->fetch();
            }

            if ($record["total"] > 0){
                return true;
            }else{
                return false;
            }
         }

        
        public function addProfile(){
            
            $this->role_id = (int)$this->role_id;

            $sql = "INSERT INTO profile(role_id, fname, mname, lname, phone_num, address, sex, dob, join_date, status, created_at, updated_at) VALUES(:role_id, :fname, :mname, :lname, :phone_num, :address, :sex, :dob, :join_date, :status, :created_at, :updated_at)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":role_id", $this->role_id, PDO::PARAM_INT);
            $query->bindParam(":fname", $this->fname);
            $query->bindParam(":mname", $this->mname);
            $query->bindParam(":lname", $this->lname);
            $query->bindParam(":phone_num", $this->phone_num);
            $query->bindParam(":address", $this->address);
            $query->bindParam(":sex", $this->sex);
            $query->bindParam(":dob", $this->dob);
            $query->bindParam(":join_date", $this->join_date);
            $query->bindParam(":status", $this->status);
            $query->bindParam(":created_at", $this->created_at);
            $query->bindParam(":updated_at", $this->updated_at);
        
            return $query->execute();
        }

        public function fetchProfile($pid){
            $sql = "SELECT * FROM profile WHERE id=:id";
            $query = $this->connect()->prepare($sql);
            $pid = (int)$pid;
            $query->bindParam(":id", $pid, PDO::PARAM_INT);
            if ($query->execute()){
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function editProfile($pid){
            
            $this->role_id = (int)$this->role_id;

            $sql = "UPDATE profile SET role_id = :role_id, fname = :fname, mname = :mname, lname = :lname, phone_num = :phone_num, address = :address, sex = :sex, dob = :dob, join_date = :join_date, status = :status, created_at = :created_at, updated_at = :updated_at WHERE id = :id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":role_id", $this->role_id, PDO::PARAM_INT);
            $query->bindParam(":fname", $this->fname);
            $query->bindParam(":mname", $this->mname);
            $query->bindParam(":lname", $this->lname);
            $query->bindParam(":phone_num", $this->phone_num);
            $query->bindParam(":address", $this->address);
            $query->bindParam(":sex", $this->sex);
            $query->bindParam(":dob", $this->dob);
            $query->bindParam(":join_date", $this->join_date);
            $query->bindParam(":status", $this->status);
            $query->bindParam(":created_at", $this->created_at);
            $query->bindParam(":updated_at", $this->updated_at);
            $pid = (int)$pid;
            $query->bindParam(":id", $pid, PDO::PARAM_INT);
            return $query->execute();
        }

        public function removeProfile($pid){
            $sql = "DELETE FROM profile WHERE id=:id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":id", $pid);
            return $query->execute();
        }

        // public function viewMember($search = "", $sex = ""){
        //     $sql = "SELECT * FROM members WHERE CONCAT(fname, ' ', lname) LIKE CONCAT('%',:search,'%') AND sex=:sex ORDER BY member_id ASC";
        //     $query = $this->connect()->prepare($sql);
        //     $query->bindParam(":search", $search);
        //     $query->bindParam(":sex", $sex);

        //     if($query->execute()){
        //         return $query->fetchAll();
        //     } else {
        //         return null;
        //     }
        // }

        public function viewProfile($search = "", $role_id = "", $status = "", $sex = "") {
            $sql = "SELECT * FROM profile WHERE CONCAT(fname, ' ', lname) LIKE CONCAT('%', :search, '%')";

            $params = [':search' => $search];

            if ($role_id !== "") {
                $sql .= " AND role_id = :role_id";
                $params[':role_id'] = (int)$role_id;
            }

            if ($sex !== "") {
                $sql .= " AND sex = :sex";
                $params[':sex'] = $sex;
            }

            if ($status !== "") {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }

            $sql .= " ORDER BY id ASC";

            $query = $this->connect()->prepare($sql);

            foreach ($params as $k => $v) {
                if ($k === ':role_id') {
                    $query->bindValue($k, $v, PDO::PARAM_INT);
                } else {
                    $query->bindValue($k, $v);
                }
            }

            if ($query->execute()) {
                return $query->fetchAll();
            } else {
                return null;
            }
        }
    }