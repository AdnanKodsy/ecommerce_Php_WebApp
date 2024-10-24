<?php
require_once 'Database\Database.php';
require_once 'ProductModels\DVD.php';
require_once 'ProductModels\Book.php';
require_once 'ProductModels\Furniture.php';
require_once 'ProductModels\productManager.php';
require_once 'Controllers\ProductController.php';

Database::init('localhost', 'root', '', 'productsys');
/*
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if ($method == 'GET' && $uri === '/scandiweb/products') {
    $controller = new ProductController();
    $controller->getAllProducts();
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['message' => 'Endpoint not found']);
}
*/
$controller = new ProductController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/scandiweb/products/save') {
    $controller->saveProduct();
} else {
    echo json_encode(['message' => 'Endpoint not found']);
}
 
Database::close();