<?php
namespace SCANDIWEB;
use SCANDIWEB\Controllers\ProductController;
use SCANDIWEB\Database\Database;
require_once 'vendor/autoload.php';


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
