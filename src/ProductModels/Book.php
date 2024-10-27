<?php
namespace SCANDIWEB\ProductModels;
class Book extends AbstractProduct
{
    private $weight;

    public const propertyName = "weight";
    public function __construct(array $productInfo)
    {
        parent::__construct($productInfo);
        if (isset($productInfo['weight'])) {
            $this->setWeight($productInfo['weight']);
        }
    }


    public function getWeight()
    {
        return $this->weight;
    }
    public function setWeight($weight)
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
        $property = $this->getWeight();
        $stmt->bind_param("id", $id, $property);
        $stmt->execute();
        $stmt->close();
    }

    public static function fetchAll()
    {
        $conn = self::getConnection();

        $stmt = $conn->prepare("SELECT products.id, products.sku, products.name, products.price, books.weight 
                                FROM products 
                                JOIN books ON products.id = books.product_id 
                                WHERE products.product_type = 'Book'");

        $stmt->execute();

        $result = $stmt->get_result();
        $products = [];

        while ($data = $result->fetch_assoc()) {
            $productInfo = [
                'id' => $data['id'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'price' => $data['price'],
                'weight' => $data['weight']
            ];

            $book = new self($productInfo);
            $products[] = $book;
        }

        $stmt->close();
        return $products;
    }
}

