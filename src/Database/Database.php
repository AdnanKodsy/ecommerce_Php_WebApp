<?php
namespace SCANDIWEB\Database;
class Database {
    private static $conn;

    public static function init($mysqli) {
        self::$conn = $mysqli;
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
