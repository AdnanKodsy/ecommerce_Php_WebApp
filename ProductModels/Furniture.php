<?php
require_once 'ProductModels\AbstractProduct.php';
class Furniture extends AbstractProduct
{
    private $dimensions;

    public function __construct()
    {
    }

    public function getProperty()
    {
        return $this->dimensions;
    }
    public function setProperty($dimensions)
    {

        $this->dimensions = $dimensions;
    }

    public function save()
    {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'Furniture')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO furniture (product_id, dimensions) VALUES (?, ?)");
        $id = $this->id;
        $property = $this->getProperty();
        $stmt->bind_param("is", $id, $property);
        $stmt->execute();
        $stmt->close();
    }


    public function fetchById($id)
    {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("SELECT products.id, products.sku, products.name, products.price, furniture.dimensions 
                                FROM products 
                                JOIN furniture ON products.id = furniture.product_id 
                                WHERE products.id = ?");
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            $this->setSku($data['sku']);
            $this->setName($data['name']);
            $this->setPrice($data['price']);
            $this->setProperty($data['dimensions']);
        }

        $stmt->close();
        return $data;
    }
}
