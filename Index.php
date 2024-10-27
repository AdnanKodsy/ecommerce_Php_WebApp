<?php
namespace SCANDIWEB;
use SCANDIWEB\Controllers\endpointController;
use SCANDIWEB\Database\Database;

require_once __DIR__ . '/vendor/autoload.php';

Database::init('localhost', 'root', '', 'productsys');

$controller = new endpointController();
$controller->resolveRequest();