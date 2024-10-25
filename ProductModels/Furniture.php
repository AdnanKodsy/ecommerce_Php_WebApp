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
}
