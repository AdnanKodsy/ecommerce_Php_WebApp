<?php
require_once 'ProductModels\AbstractProduct.php';
class Book extends AbstractProduct
{
    private $weight;

    public function __construct()
    {
    }


    public function getProperty()
    {
        return $this->weight;
    }
    public function setProperty($weight)
    {

        $this->weight = $weight;
    }


    public function save()
    {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'Book')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO books (product_id, weight) VALUES (?, ?)");
        $id = $this->id;
        $property = $this->getProperty();
        $stmt->bind_param("id", $id, $property);
        $stmt->execute();
        $stmt->close();
    }
}

