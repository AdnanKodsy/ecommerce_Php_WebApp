<?php
namespace SCANDIWEB;
use SCANDIWEB\Controllers\endpointController;
use SCANDIWEB\Database\Database;
use SCANDIWEB\Database\DatabaseConfig;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $dbConnection = DatabaseConfig::getConnection();
    Database::init($dbConnection); // Adjust your Database class to accept a connection
} catch (\Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$controller = new endpointController();
$controller->resolveRequest();