<?php

require 'ProductModels\Product.php';
class DVD extends Product {
    private $size;

    public function __construct($sku, $name, $price, $size) {
        parent::__construct($sku, $name, $price);
        $this->setSize($size);
    }

    // Getter and Setter for size
    public function getSize() {
        return $this->size;
    }

    public function setSize($size) {
        $this->size = $size;
    }

    // Save method to save DVD data in both 'products' and 'dvds' tables
    public function save($conn) {
        // Save to 'products' table
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'DVD')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        // Save to 'dvds' table
        $stmt = $conn->prepare("INSERT INTO dvds (product_id, size) VALUES (?, ?)");
        $stmt->bind_param("id", $this->id, $this->size);
        $stmt->execute();
    }
}

?>