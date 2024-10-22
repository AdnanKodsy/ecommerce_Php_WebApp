<?php
require_once 'Database\Database.php';
require_once 'ProductModels\DVD.php';
require_once 'ProductModels\Book.php';
require_once 'ProductModels\Furniture.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "productsys";

// Create a new instance of the Database class
$db = new Database($host, $user, $password, $dbname);

// Create and save DVD
$dvd = new DVD("DVD001", "Inception", 19.99, 700);
$dvd->save($db->getConnection());

// Create and save Book
$book = new Book("BOOK001", "The Great Gatsby", 10.99, 1.2);
$book->save($db->getConnection());

// Create and save Furniture
$furniture = new Furniture("FURN001", "Sofa", 299.99, "200x80x100");
$furniture->save($db->getConnection());
?>