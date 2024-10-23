<?php
class Database {
    private static $conn;

    public static function init($host, $user, $password, $dbname) {
        if (self::$conn === null) {
            self::$conn = new mysqli($host, $user, $password, $dbname);

            if (self::$conn->connect_error) {
                die("Connection failed: " . self::$conn->connect_error);
            }
        }
    }

    public static function getConnection() {
        return self::$conn;
    }

    public static function close() {
        if (self::$conn) {
            self::$conn->close();
        }
    }

    public static function query($sql) {
        return self::$conn->query($sql);
    }

    public static function prepare($sql) {
        return self::$conn->prepare($sql);
    }

    public static function escape($value) {
        return self::$conn->real_escape_string($value);
    }
}
