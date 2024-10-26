<?php

class ProductManager extends Database
{
    public function __construct()
    {
    }

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
        $product = new $productType($data);
        $product->save();

        return ["message" => "Product saved successfully."];
    }



    public function displayAll()
    {
        $query = "SELECT id, product_type FROM products ORDER BY id ASC;";
        $result = $this->query($query);
        
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $productId = $row['id'];
            $productType = $row['product_type'];
    
            $reflectionClass = new ReflectionClass($productType);
            $productInstance = $reflectionClass->newInstanceWithoutConstructor();
            $productMethod = new ReflectionMethod($productType, 'fetchById');
            $fetchedData = $productMethod->invoke($productInstance, $productId);

            if ($fetchedData) {
                $propertyName = $productType::propertyName;
                $products[] = [
                    'ID' => $fetchedData['id'],
                    'SKU' => $productInstance->getSku(),
                    'Name' => $productInstance->getName(),
                    'Price' => "$" . number_format($productInstance->getPrice(), 2),
                    'Type' => $productType,
                    $propertyName => $productInstance->{"get" . ucfirst($propertyName)}(),
                ];
            }
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
