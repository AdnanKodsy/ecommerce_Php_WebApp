<?php

class ProductManager extends Database {
    // Constructor to initialize the Database connection by extending the parent constructor
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

        while ($row = $result->fetch_assoc()) {
            $product = null;
            switch ($row['product_type']) {
                case 'DVD':
                    $product = new DVD($row['sku'], $row['name'], $row['price'], $row['dvd_size']);
                    break;
                case 'Book':
                    $product = new Book($row['sku'], $row['name'], $row['price'], $row['book_weight']);
                    break;
                case 'Furniture':
                    $product = new Furniture($row['sku'], $row['name'], $row['price'], $row['furniture_dimensions']);
                    break;
            }

            if ($product) {
                echo "ID: " . $row['id'] . "<br>";
                echo "SKU: " . $product->getSku() . "<br>";
                echo "Name: " . $product->getName() . "<br>";
                echo "Price: $" . $product->getPrice() . "<br>";
                echo "Type: " . $row['product_type'] . "<br>";

                switch ($row['product_type']) {
                    case 'DVD':
                        echo "Size: " . $product->getSize() . " MB<br>";
                        break;
                    case 'Book':
                        echo "Weight: " . $product->getWeight() . " Kg<br>";
                        break;
                    case 'Furniture':
                        echo "Dimensions: " . $product->getDimensions() . "<br>";
                        break;
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
