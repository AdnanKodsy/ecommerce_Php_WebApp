<?php

class ProductManager extends Database
{
    public function __construct()
    {
    }
    private $propertyColumns = [
        'DVD' => 'size',
        'Book' => 'weight',
        'Furniture' => 'dimensions',
    ];

    private function skuExists($sku)
    {
        $query = "SELECT COUNT(*) AS count FROM products WHERE sku = ?";
        $conn = self::getConnection();
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['count'] > 0;
    }

    public function createAndSaveProduct($jsonData)
    {
        $data = json_decode($jsonData, true);

        if ($this->skuExists($data['sku'])) {
            return ["message" => "SKU already exists."];
        }

        $productType = $data['product_type'];
        $product = new $productType();
        $this->setCommonProperties($product, $data);
        $rowName = $this->propertyColumns[$productType];

        $product->setProperty($data[$rowName]);

        $product->save();

        return ["message" => "Product saved successfully."];
    }

    private function setCommonProperties($product, $data)
    {
        $product->setSku($data['sku']);
        $product->setName($data['name']);
        $product->setPrice($data['price']);
    }


    public function displayAll()
{
    $query = "SELECT id, product_type FROM products ORDER BY id ASC;";
    $result = $this->query($query);
    
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $productId = $row['id'];
        $productType = $row['product_type'];

        $product = new $productType();

        $productData = $product->fetchById($productId);

        $products[] = [
            'ID' => $productData['id'],
            'SKU' => $product->getSku(),
            'Name' => $product->getName(),
            'Price' => "$" . number_format($product->getPrice(), 2),
            'Type' => $productType,
            $this->propertyColumns[$productType] => $product->getProperty(),
        ];
    }

    return $products;
}




    public function deleteProductsByIds($ids)
    {
        if (empty($ids)) {
            echo "No IDs provided for deletion.<br>";
            return;
        }

        $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));

        $deleteQueries = [
            "DELETE FROM dvds WHERE product_id IN ($idPlaceholders)",
            "DELETE FROM books WHERE product_id IN ($idPlaceholders)",
            "DELETE FROM furniture WHERE product_id IN ($idPlaceholders)",
            "DELETE FROM products WHERE id IN ($idPlaceholders)"
        ];

        foreach ($deleteQueries as $query) {
            $stmt = $this->prepare($query);
            $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
            $stmt->execute();
            $stmt->close();
        }

        echo "Deleted products with IDs: " . implode(', ', $ids) . "<br>";
    }
}
