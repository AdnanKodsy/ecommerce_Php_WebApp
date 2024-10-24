<?php
require_once 'ProductModels\ProductManager.php'; // Your existing ProductManager class

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
    public function saveProduct() {
        $input = json_decode(file_get_contents('php://input'), true);

        $productType = $input['product_type'];
        $productClassMap = [
            'DVD' => ['class' => DVD::class, 'params' => ['sku', 'name', 'price', 'size']],
            'Book' => ['class' => Book::class, 'params' => ['sku', 'name', 'price', 'weight']],
            'Furniture' => ['class' => Furniture::class, 'params' => ['sku', 'name', 'price', 'dimensions']],
        ];

        if (!array_key_exists($productType, $productClassMap)) {
            echo json_encode(['message' => 'Invalid product type']);
            return;
        }

        // Create product instance
        $productClassInfo = $productClassMap[$productType];
        $product = new $productClassInfo['class']();

        // Use setters to assign values dynamically
        foreach ($productClassInfo['params'] as $param) {
            if (isset($input[$param])) {
                $setterMethod = 'set' . ucfirst($param);
                if (method_exists($product, $setterMethod)) {
                    $product->$setterMethod($input[$param]);
                }
            }
        }

        // Call the saveProduct method to save it in the database
        $this->productManager->saveProduct($product);
        echo json_encode(['message' => 'Product saved successfully']);
    }
}

