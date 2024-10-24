<?php
require_once 'ProductModels\ProductManager.php'; // Your existing ProductManager class

class ProductController {
    private $productManager;

    public function __construct() {
        $this->productManager = new ProductManager();
    }

    public function getAllProducts() {
        header('Content-Type: application/json'); // Set response header for JSON
        try {
            $products = $this->productManager->displayAll(); // Assume this fetches products in array format
            echo json_encode($products);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

