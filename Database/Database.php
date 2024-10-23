<?php
class Database {
    private static $conn;

    // Static method to initialize the connection
    public static function init($host, $user, $password, $dbname) {
        if (self::$conn === null) {
            self::$conn = new mysqli($host, $user, $password, $dbname);

            // Check for connection errors
            if (self::$conn->connect_error) {
                die("Connection failed: " . self::$conn->connect_error);
            }
        }
    }

    // Method to get the database connection
    public static function getConnection() {
        return self::$conn;
    }

    // Close the database connection
    public static function close() {
        if (self::$conn) {
            self::$conn->close();
        }
    }

    // Method for executing queries
    public static function query($sql) {
        return self::$conn->query($sql);
    }

    // Method for preparing statements
    public static function prepare($sql) {
        return self::$conn->prepare($sql);
    }

    // Escaping values
    public static function escape($value) {
        return self::$conn->real_escape_string($value);
    }
}
