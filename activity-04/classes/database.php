<?php

class Database{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "library";

    protected $conn;

    public function connect(){
        $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname",$this->username,
        $this->password);

        return $this->conn;
    }
}

// $obj=new Database();
// var_dump($obj->connect());