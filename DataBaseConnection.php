<?php

class DataBaseConnection
{
    protected $conn = null;
    protected $ServerName = "localhost";
    protected $userName = "root";
    protected $password = "";
    protected $DBName = "productsys";

    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->ServerName;dbname=$this->DBName", $this->userName, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

    }
}
?>