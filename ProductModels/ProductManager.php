<?php

class ProductManager extends Database {
    public function __construct() {
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

        $productClassMap = [
            'DVD' => ['class' => DVD::class, 'params' => ['sku', 'name', 'price', 'dvd_size'], 'detailLabel' => 'Size'],
            'Book' => ['class' => Book::class, 'params' => ['sku', 'name', 'price', 'book_weight'], 'detailLabel' => 'Weight'],
            'Furniture' => ['class' => Furniture::class, 'params' => ['sku', 'name', 'price', 'furniture_dimensions'], 'detailLabel' => 'Dimensions'],
        ];
    
        while ($row = $result->fetch_assoc()) {
            $product = null;
            $productType = $row['product_type'];
    
            if (array_key_exists($productType, $productClassMap)) {
                $productClassInfo = $productClassMap[$productType];
                $productClass = $productClassInfo['class'];
    
                $params = array_map(function($param) use ($row) {
                    return $row[$param];
                }, $productClassInfo['params']);
    
                $product = new $productClass(...$params);
    
                $details = [
                    'ID' => $row['id'],
                    'SKU' => $product->getSku(),
                    'Name' => $product->getName(),
                    'Price' => "$" . number_format($product->getPrice(), 2),
                    'Type' => $productType,
                    $productClassInfo['detailLabel'] => $row[$productClassInfo['params'][3]],
                ];
    
                foreach ($details as $label => $value) {
                    echo "$label: $value<br>";
                }
                echo "<br>";
            }
        }
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
