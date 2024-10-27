<?php
namespace SCANDIWEB\ProductModels;
use SCANDIWEB\Database\DataBase;
abstract class AbstractProduct extends Database
{
    protected $id;
    protected $sku;
    protected $name;
    protected $price;

    public function __construct(array $productInfo)
    {
        if (isset($productInfo['id'])) {
            $this->setId($productInfo['id']);
        }
        if (isset($productInfo['sku'])) {
            $this->setSku($productInfo['sku']);
        }
        if (isset($productInfo['name'])) {
            $this->setName($productInfo['name']);
        }
        if (isset($productInfo['price'])) {
            $this->setPrice($productInfo['price']);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    abstract public function save();
    abstract public static function fetchAll();
}
