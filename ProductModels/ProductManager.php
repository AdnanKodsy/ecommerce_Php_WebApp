<?php

class ProductManager extends Database {
    public function __construct() {
    }
    private $productClassMap = [
        'DVD' => DVD::class,
        'Book' => Book::class,
        'Furniture' => Furniture::class,
    ];

    private function skuExists($sku) {
        $query = "SELECT COUNT(*) AS count FROM products WHERE sku = ?";
        $conn = self::getConnection(); // Assuming you have a method to get DB connection
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'] > 0; 
    }

    public function createAndSaveProduct($jsonData) {
        $data = json_decode($jsonData, true);

        if (!isset($data['product_type']) || !array_key_exists($data['product_type'], $this->productClassMap)) {
            return ["message" => "Invalid product type."];
        }
        if ($this->skuExists($data['sku'])) {
            return ["message" => "SKU already exists."];
        }

        $productClass = $this->productClassMap[$data['product_type']];
        $product = new $productClass();
        $this->setCommonProperties($product, $data);
        

        foreach ($data as $key => $value) {
            if (in_array($key, ['sku', 'name', 'price', 'product_type'])) {
                continue;
            }
            $product->setProperty($key, $value);
        }
        $product->save();
        
        return ["message" => "Product saved successfully."];
    }

    private function setCommonProperties($product, $data) {
        $product->setSku($data['sku']);
        $product->setName($data['name']);
        $product->setPrice($data['price']);
    }
    
   
    public function displayAll() {
        $query = "
            SELECT p.id, p.sku, p.name, p.price, p.product_type,
                d.size AS dvd_size, b.weight AS book_weight, f.dimensions AS furniture_dimensions
            FROM products p
            LEFT JOIN dvds d ON p.id = d.product_id
            LEFT JOIN books b ON p.id = b.product_id
            LEFT JOIN furniture f ON p.id = f.product_id
            ORDER BY p.id ASC;
        ";
    
        $result = $this->query($query);
        $products = [];
    

        $propertySetters = [
            'DVD' => 'setSize',
            'Book' => 'setWeight',
            'Furniture' => 'setDimensions',
        ];
    
        $propertyColumns = [
            'DVD' => 'dvd_size',
            'Book' => 'book_weight',
            'Furniture' => 'furniture_dimensions',
        ];
    
        $propertyGetters = [
            'DVD' => 'getSize',
            'Book' => 'getWeight',
            'Furniture' => 'getDimensions',
        ];
    
        while ($row = $result->fetch_assoc()) {
            $productType = $row['product_type'];
    
            if (array_key_exists($productType, $this->productClassMap)) {
                $productClass = $this->productClassMap[$productType];
                $product = new $productClass();
 
                $product->setSku($row['sku']);
                $product->setName($row['name']);
                $product->setPrice($row['price']);
    
                $setterMethod = $propertySetters[$productType];
                $columnName = $propertyColumns[$productType];
                $product->$setterMethod($row[$columnName]);
    
                $products[] = [
                    'ID' => $row['id'],
                    'SKU' => $product->getSku(),
                    'Name' => $product->getName(),
                    'Price' => "$" . number_format($product->getPrice(), 2),
                    'Type' => $productType,
                    $columnName => $product->{$propertyGetters[$productType]}(),
                ];
            }
        }
        return $products;
    }
    
    
    

    public function deleteProductsByIds($ids) {
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
