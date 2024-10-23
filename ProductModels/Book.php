<?php 
require_once 'ProductModels\AbstractProduct.php';
class Book extends AbstractProduct {
    private $weight;

    public function __construct($sku, $name, $price, $weight) {
        parent::__construct($sku, $name, $price);
        $this->setWeight($weight);
    }

    public function getWeight() {
        return $this->weight;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }


    public function save() {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'Book')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO books (product_id, weight) VALUES (?, ?)");
        $stmt->bind_param("id", $this->id, $this->weight);
        $stmt->execute();
        $stmt->close();
    }
}

