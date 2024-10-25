<?php
require_once 'Database\Database.php';
require_once 'ProductModels\DVD.php';
require_once 'ProductModels\Book.php';
require_once 'ProductModels\Furniture.php';
require_once 'ProductModels\productManager.php';
require_once 'Controllers\ProductController.php';


Database::init('localhost', 'root', '', 'productsys');
$productController = new ProductController();

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestUri === '/scandiweb/products' && $requestMethod === 'GET') {

    $productController->getAllProducts();
} elseif ($requestUri === '/scandiweb/products/save' && $requestMethod === 'POST') {

    $productController->createProduct();
} else {
    header("HTTP/1.0 404 Not Found");
    echo json_encode(["message" => "Not Found"]);
}
