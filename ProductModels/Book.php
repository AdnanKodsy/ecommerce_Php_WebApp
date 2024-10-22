<?php 
require_once 'ProductModels\Product.php';
class Book extends Product {
    private $weight;

    public function __construct($sku, $name, $price, $weight) {
        parent::__construct($sku, $name, $price);
        $this->setWeight($weight);
    }

    // Getter and Setter for weight
    public function getWeight() {
        return $this->weight;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    // Save method to save Book data in both 'products' and 'books' tables
    public function save($conn) {
        // Save to 'products' table
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'Book')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        // Save to 'books' table
        $stmt = $conn->prepare("INSERT INTO books (product_id, weight) VALUES (?, ?)");
        $stmt->bind_param("id", $this->id, $this->weight);
        $stmt->execute();
    }
}

?>