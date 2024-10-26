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

    public function fetchById($id)
    {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("SELECT products.id, products.sku, products.name, products.price, books.weight 
                                FROM products 
                                JOIN books ON products.id = books.product_id 
                                WHERE products.id = ?");
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            $this->setSku($data['sku']);
            $this->setName($data['name']);
            $this->setPrice($data['price']);
            $this->setProperty($data['weight']);
        }

        $stmt->close();
        return $data;
    }
}

