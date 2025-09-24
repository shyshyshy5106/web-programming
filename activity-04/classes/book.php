<?php
require_once "database.php";

class Books{
    public $id = "";
    public $title = "";
    public $author = "";
    public $genre = "";
    public $publication_year = "";
    public $publisher = "";
    public $copies = "";

    protected $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function addBook(){
        if ($this->isBookExist($this->title)) {
            return false;
        }
        $sql = "INSERT INTO books (title, author, genre, publication_year,publisher,copies) VALUES (:title, :author, :genre, :publication_year, :publisher, :copies)";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(":title", $this->title);
        $query->bindParam(":author", $this->author);
        $query->bindParam(":genre", $this->genre);
        $query->bindParam(":publication_year", $this->publication_year);
        $query->bindParam(":publisher", $this->publisher);
        $query->bindParam(":copies", $this->copies);

        return $query->execute();
    }

    public function viewBook ($search = "", $genre = ""){
        $sql = "SELECT * FROM books WHERE title LIKE CONCAT('%',:search,'%') AND genre LIKE CONCAT('%',:genre,'%') ORDER BY id  ASC";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(":search", $search);
        $query->bindParam(":genre", $genre);

        if($query->execute()){
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function isBookExist($ptitle){
        $sql = "SELECT COUNT(*) as total FROM books WHERE title = :title";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(":title", $ptitle);
        $record = NULL;
        if($query->execute()){
            $record = $query->fetch();
        }
        if($record["total"] > 0){
            return true;
        } else {
            return false;
        }
    }
}