<?php
//use DataBaseConnection;
require_once 'DataBaseConnection.php';
class Crud extends DataBaseConnection
{

    
protected $sku = 784554877;
protected $namee = "test";
protected $price = 33.3;

public function saveProduct()
{
    try{
        $this->conn->beginTransaction();
        $sql = "INSERT INTO product (sku, namee, price)
         VALUES ($this->sku, '$this->namee', $this->price)";
        $this->conn->exec($sql);
        echo "vlaue inserted in DB";
        $this->conn->commit();
        return true;

    }catch (PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
        return false;
    }
}


}

?>