<?php
require_once 'ProductModels\Product.php';
class Furniture extends Product {
    private $dimensions;

    public function __construct($sku, $name, $price, $dimensions) {
        parent::__construct($sku, $name, $price);
        $this->setDimensions($dimensions);
    }

    // Getter and Setter for dimensions
    public function getDimensions() {
        return $this->dimensions;
    }

    public function setDimensions($dimensions) {
        $this->dimensions = $dimensions;
    }

    // Save method to save Furniture data in both 'products' and 'furniture' tables
    public function save($conn) {
        // Save to 'products' table
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'Furniture')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        // Save to 'furniture' table
        $stmt = $conn->prepare("INSERT INTO furniture (product_id, dimensions) VALUES (?, ?)");
        $stmt->bind_param("is", $this->id, $this->dimensions);
        $stmt->execute();
    }
}
?>