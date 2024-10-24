<?php
require_once 'Database\Database.php';
require_once 'ProductModels\DVD.php';
require_once 'ProductModels\Book.php';
require_once 'ProductModels\Furniture.php';
require_once 'ProductModels\productManager.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "productsys";

Database::init('localhost', 'root', '', 'productsys');

/*
$dvd = new DVD("DVD001", "Inception", 19.99, 700);
$dvd->save();


$book = new Book("BOOK001", "The Great Gatsby", 10.99, 1.2);
$book->save();


$furniture = new Furniture("FURN001", "Sofa", 299.99, "200x80x100");
$furniture->save();
*/
$productManager = new ProductManager();

// Display all products
$productManager->displayAll();

// Delete products by IDs
//$productManager->deleteProductsByIds([22, 24]);

// Close the database connection when done
Database::close();
