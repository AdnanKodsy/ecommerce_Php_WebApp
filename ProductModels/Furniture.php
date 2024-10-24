<?php
require_once 'ProductModels\AbstractProduct.php';
class Furniture extends AbstractProduct {
    private $dimensions;

    public function __construct() {
    }

    public function getDimensions() {
        return $this->dimensions;
    }

    public function setDimensions($dimensions) {
        $this->dimensions = $dimensions;
    }

    public function save() {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'Furniture')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO furniture (product_id, dimensions) VALUES (?, ?)");
        $stmt->bind_param("is", $this->id, $this->dimensions);
        $stmt->execute();
        $stmt->close();
    }
}
