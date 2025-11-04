<?php

    require_once 'database.php';

    class Roles extends Database{
        public $role_id = "";
        public $role_name = "";
        public $description = "";

        public function isRoleExist($prole_name, $prole_id = "") {
            $role_id = (int)$prole_id;
            
            $sql = "SELECT COUNT(*) as total FROM roles WHERE role_name = :role_name AND role_id <> :role_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":role_name", $prole_name);
            $query->bindParam(":role_id", $role_id, PDO::PARAM_INT);
            
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

        public function addRole() {
            if ($this->isRoleExist($this->role_name)) {
                return false;
            }

            $sql = "INSERT INTO roles(role_name, description) VALUES(:role_name, :description)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":role_name", $this->role_name);
            $query->bindParam(":description", $this->description);
            
            return $query->execute();
        }

        public function fetchRole($prole_id) {
            $role_id = (int)$prole_id;
            
            $sql = "SELECT * FROM roles WHERE role_id = :role_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":role_id", $role_id, PDO::PARAM_INT);
            
            if ($query->execute()) {
                return $query->fetch();
            }
            return null;
        }

        public function editRole($prole_id) {
            if ($this->isRoleExist($this->role_name, $prole_id)) {
                return false;
            }

            $role_id = (int)$prole_id;
            
            $sql = "UPDATE roles SET role_name = :role_name, description = :description WHERE role_id = :role_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":role_name", $this->role_name);
            $query->bindParam(":description", $this->description);
            $query->bindParam(":role_id", $role_id, PDO::PARAM_INT);
            
            return $query->execute();
        }

        public function removeRole($prole_id) {
            $role_id = (int)$prole_id;
            
            $sql = "DELETE FROM roles WHERE role_id = :role_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":role_id", $role_id, PDO::PARAM_INT);
            
            return $query->execute();
        }

        public function viewRole($search = "") {
            $sql = "SELECT * FROM roles";
            $params = [];
            
            if (!empty($search)) {
                $sql .= " WHERE role_name LIKE CONCAT('%', :search, '%')";
                $params[':search'] = $search;
            }
            
            $sql .= " ORDER BY role_id ASC";
            
            $query = $this->connect()->prepare($sql);
            
            foreach ($params as $key => $value) {
                $query->bindValue($key, $value);
            }

            if ($query->execute()) {
                return $query->fetchAll();
            }
            return null;
        }
    }