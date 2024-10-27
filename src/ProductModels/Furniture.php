<?php
namespace SCANDIWEB\ProductModels;
class Furniture extends AbstractProduct
{
    private $dimensions;
    public const propertyName = 'dimensions';

    public function __construct(array $productInfo)
    {
        parent::__construct($productInfo);

        if (isset($productInfo['dimensions'])) {
            $this->setDimensions($productInfo['dimensions']);
        }
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }
    public function setDimensions($dimensions)
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
        $property = $this->getDimensions();
        $stmt->bind_param("is", $id, $property);
        $stmt->execute();
        $stmt->close();
    }


    public static function fetchAll()
    {
        $conn = self::getConnection();

        $stmt = $conn->prepare("SELECT products.id, products.sku, products.name, products.price, furniture.dimensions 
                                FROM products 
                                JOIN furniture ON products.id = furniture.product_id 
                                WHERE products.product_type = 'Furniture'");

        $stmt->execute();

        $result = $stmt->get_result();
        $products = [];

        while ($data = $result->fetch_assoc()) {
            $productInfo = [
                'id' => $data['id'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'price' => $data['price'],
                'dimensions' => $data['dimensions']
            ];

            $furniture = new self($productInfo);
            $products[] = $furniture;
        }

        $stmt->close();
        return $products;
    }
}
