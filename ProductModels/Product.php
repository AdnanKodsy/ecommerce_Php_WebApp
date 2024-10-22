<?php
abstract class Product {
    protected $id;
    protected $sku;
    protected $name;
    protected $price;

    public function __construct($sku, $name, $price) {
        $this->setSku($sku);
        $this->setName($name);
        $this->setPrice($price);
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getSku() {
        return $this->sku;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    // Setters
    public function setSku($sku) {
        $this->sku = $sku;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    // Abstract method to be implemented by child classes
    abstract public function save($conn);

    // Display method for retrieving and displaying all products with specific attributes
    public static function display($conn) {
        // SQL to retrieve all products and join with specific tables based on product_type
        $query = "
            SELECT p.id, p.sku, p.name, p.price, p.product_type,
                d.size AS dvd_size, b.weight AS book_weight, f.dimensions AS furniture_dimensions
            FROM products p
            LEFT JOIN dvds d ON p.id = d.product_id
            LEFT JOIN books b ON p.id = b.product_id
            LEFT JOIN furniture f ON p.id = f.product_id
            ORDER BY p.id ASC;
        ";

        $result = $conn->query($query);

        // Display the results
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . "<br>";
            echo "SKU: " . $row['sku'] . "<br>";
            echo "Name: " . $row['name'] . "<br>";
            echo "Price: $" . $row['price'] . "<br>";
            echo "Type: " . $row['product_type'] . "<br>";

            // Display specific attributes based on the product type
            switch ($row['product_type']) {
                case 'DVD':
                    echo "Size: " . $row['dvd_size'] . " MB<br>";
                    break;
                case 'Book':
                    echo "Weight: " . $row['book_weight'] . " Kg<br>";
                    break;
                case 'Furniture':
                    echo "Dimensions: " . $row['furniture_dimensions'] . "<br>";
                    break;
            }
            echo "<br>";
        }
    }
}

?>