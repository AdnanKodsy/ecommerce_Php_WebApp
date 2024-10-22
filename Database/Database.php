<?php

class Database {
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $conn;

    // Constructor to initialize database connection details
    public function __construct($host, $user, $password, $dbname) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->connect();
    }

    // Method to establish a connection to the database
    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);

        // Check if the connection was successful
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to return the current connection instance
    public function getConnection() {
        return $this->conn;
    }

    // Method to close the database connection
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // Method to execute a query (for custom SQL queries)
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // Method to prepare statements
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    // Method to escape user input (to prevent SQL injection)
    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }
}
?>