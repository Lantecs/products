<?php
class Database {
    private $host = "127.0.0.1"; 
    private $username = "root"; 
    private $password = "";     
    private $db_name = "it113";  
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
          
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully"; 

        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>