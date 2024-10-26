<?php

namespace SCANDIWEB\ProductModels;
class DVD extends AbstractProduct
{
    private $size;
    public const propertyName = "size";

    public function __construct(array $productInfo)
    {
        parent::__construct($productInfo);

        if (isset($productInfo['size'])) {
            $this->setSize($productInfo['size']);
        }
    }

    public function getSize()
    {
        return $this->size;
    }
    public function setSize($size)
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
        $property = $this->getSize();
        $stmt->bind_param("id", $id, $property);
        $stmt->execute();
        $stmt->close();
    }

    public function fetchById($id)
    {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("SELECT products.id, products.sku, products.name, products.price, dvds.size 
                                FROM products 
                                JOIN dvds ON products.id = dvds.product_id 
                                WHERE products.id = ?");
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            $this->setSku($data['sku']);
            $this->setName($data['name']);
            $this->setPrice($data['price']);
            $this->setSize($data['size']);
        }

        $stmt->close();
        return $data;
    }
}

