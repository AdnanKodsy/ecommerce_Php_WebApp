<?php
namespace SCANDIWEB\Database;

class DatabaseConfig {
    private static $host = 'localhost';
    private static $username = 'root';
    private static $password = '';
    private static $dbname = 'productsys';

    public static function getConnection() {
        $mysqli = new \mysqli(self::$host, self::$username, self::$password, self::$dbname);

        if ($mysqli->connect_error) {
            throw new \Exception('Connection error: ' . $mysqli->connect_error);
        }

        return $mysqli;
    }
}
