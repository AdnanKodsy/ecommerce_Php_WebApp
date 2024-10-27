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

    public static function fetchAll()
    {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("SELECT products.id, products.sku, products.name, products.price, dvds.size 
                                FROM products 
                                JOIN dvds ON products.id = dvds.product_id 
                                WHERE products.product_type = 'DVD'");
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        $products = [];
        
        while ($data = $result->fetch_assoc()) {
            $productInfo = [
                'id' => $data['id'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'price' => $data['price'],
                'size' => $data['size']
            ];
    
            $dvd = new self($productInfo);
            $products[] = $dvd;
        }
    
        $stmt->close();
        return $products;
    }
    

}

