<?php

require_once 'database.php';

Class Admins extends Database{
    public $id;
    public $firstname;
    public $lastname;
    public $role;
    public $email;
    public $password;
    public $is_active;


    function addAdmin(){
        $sql = "INSERT INTO admin (firstname, lastname, role, email, password, is_active) VALUES 
        (:firstname, :lastname, :role, :email, :password, :is_active);";

        $query=$this->connect()->prepare($sql);
        $query->bindParam(':firstname', $this->firstname);
        $query->bindParam(':lastname', $this->lastname);
        $query->bindParam(':role', $this->role);
        $query->bindParam(':email', $this->email);
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $query->bindParam(':password', $hashedPassword);
        $query->bindParam(':is_active', $this->is_active);
        
        if($query->execute()){
            return true;
        }
        else{
            return false;
        }	
    }

    function getAdminByEmail(){
        $sql = "SELECT * FROM admin WHERE email = :email;";
        $query=$this->connect()->prepare($sql);
        $query->bindParam(':email', $this->email);
        if($query->execute()){
            $data = $query->fetch();
        }
        return $data;
    }
}

?>