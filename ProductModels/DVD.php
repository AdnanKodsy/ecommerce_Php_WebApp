<?php

require 'ProductModels\AbstractProduct.php';
class DVD extends AbstractProduct
{
    private $size;

    public function __construct()
    {
    }

    public function getProperty()
    {
        return $this->size;
    }
    public function setProperty($size)
    {
        $this->size = $size;
    }

    public function save()
    {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, 'DVD')");
        $sku = $this->getSku();
        $name = $this->getName();
        $price = $this->getPrice();
        $stmt->bind_param("ssd", $sku, $name, $price);
        $stmt->execute();
        $this->id = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO dvds (product_id, size) VALUES (?, ?)");
        $id = $this->id;
        $property = $this->getProperty();
        $stmt->bind_param("id", $id, $property);
        $stmt->execute();
        $stmt->close();
    }
}

