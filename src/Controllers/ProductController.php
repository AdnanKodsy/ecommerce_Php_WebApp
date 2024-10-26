<?php
namespace SCANDIWEB\Controllers;
use SCANDIWEB\ProductModels\ProductManager;
use Exception;

class ProductController {
    private $productManager;

    public function __construct() {
        $this->productManager = new ProductManager();
    }

    public function getAllProducts() {
        header('Content-Type: application/json');
        try {
            $products = $this->productManager->displayAll();
            echo json_encode($products);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createProduct() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $jsonData = file_get_contents('php://input');

            try {
                $response = $this->productManager->createAndSaveProduct($jsonData);
                echo json_encode($response);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(["message" => "Invalid request method. Only POST is allowed."]);
        }
    }
}
