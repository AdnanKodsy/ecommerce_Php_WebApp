<?php
namespace SCANDIWEB\ProductModels;
use SCANDIWEB\Database\Database;
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
    private function getFullClassName($className){
        $prefix = "SCANDIWEB\\ProductModels\\";
        $fullClassName = $prefix . $className;
        return $fullClassName;
    }
    private function getInstance($className ,$data){
       $instance = $this->getFullClassName($className);   
       if (!class_exists($instance)) {
        return ["message" => "Class does not exist"];
       }
       return new $instance($data);

    }

    public function createAndSaveProduct($jsonData)
    {
        $data = json_decode($jsonData, true);

        if ($this->skuExists($data['sku'])) {
            return ["message" => "SKU already exists."];
        }

        $productType = $data['product_type'];
        $product = $this->getInstance($productType, $data);
        $product->save();

        return ["message" => "Product saved successfully."];
    }



    public function displayAll()
    {
        $products = [];
        $query = "SELECT DISTINCT product_type FROM products;";
        $result = $this->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $productType = $row['product_type'];
            
            $productClass = $this->getFullClassName($productType);
            
            if (class_exists($productClass) && method_exists($productClass, 'fetchAll')) {
                $allProducts = $productClass::fetchAll();
                
                foreach ($allProducts as $productInstance) {
                    $propertyName = $productClass::propertyName;
    
                    $products[] = [
                        'ID' => $productInstance->getId(),
                        'SKU' => $productInstance->getSku(),
                        'Name' => $productInstance->getName(),
                        'Price' => "$" . number_format($productInstance->getPrice(), 2),
                        'Type' => $productType,
                        $propertyName => $productInstance->{"get" . ucfirst($propertyName)}(),
                    ];
                }
            }
        }
        usort($products, function($a, $b) {
            return $a['ID'] <=> $b['ID'];
        });
        
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
