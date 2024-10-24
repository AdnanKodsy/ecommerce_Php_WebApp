<?php

class ProductManager extends Database {
    public function __construct() {
    }

    private $productClassMap = [
        'DVD' => ['class' => DVD::class, 'params' => ['sku', 'name', 'price', 'dvd_size'], 'detailLabel' => 'Size', 'column' => 'Size', 'table' => 'dvds'],
        'Book' => ['class' => Book::class, 'params' => ['sku', 'name', 'price', 'book_weight'], 'detailLabel' => 'Weight', 'column' => 'Weight', 'table' => 'books'],
        'Furniture' => ['class' => Furniture::class, 'params' => ['sku', 'name', 'price', 'furniture_dimensions'], 'detailLabel' => 'Dimensions', 'column' => 'Dimensions', 'table' => 'furniture'],
    ];
    

    public function saveProduct($product) {
        $conn = self::getConnection();
        $type = get_class($product);
        
        if (array_key_exists($type, $this->productClassMap)) {
            $productClassInfo = $this->productClassMap[$type];
            $table = $productClassInfo['table'];
            $column = $productClassInfo['column'];
    
            $sku = $product->getSku();
            $name = $product->getName();
            $price = $product->getPrice();

            $query = "INSERT INTO products (sku, `name`, price, product_type) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssds', 
                $sku,
                $name,
                $price, 
                $type
            );
            $stmt->execute();
            $productId = $conn->insert_id;
    
            $detail = $product->{"get" . ucfirst($column)}();
            $query = "INSERT INTO $table (product_id, $column) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('is', $productId, $detail);
            $stmt->execute();
            $stmt->close();
        }
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
        while ($row = $result->fetch_assoc()) {
            $product = null;
            $productType = $row['product_type'];
    
            if (array_key_exists($productType, $this->productClassMap)) {
                $productClassInfo = $this->productClassMap[$productType];
                $productClass = $productClassInfo['class'];
                $product = new $productClass();
    

                $product->setSku($row['sku']);
                $product->setName($row['name']);
                $product->setPrice($row['price']);
                $detailSetter = 'set' . ucfirst($productClassInfo['column']);
                $product->$detailSetter($row[$productClassInfo['params'][3]]);
    
                $products[] = [
                    'ID' => $row['id'],
                    'SKU' => $product->getSku(),
                    'Name' => $product->getName(),
                    'Price' => "$" . number_format($product->getPrice(), 2),
                    'Type' => $productType,
                    $productClassInfo['detailLabel'] => $product->{'get' . ucfirst($productClassInfo['column'])}(),
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
