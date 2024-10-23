<?php
abstract class AbstractProduct extends Database {
    protected $id;
    protected $sku;
    protected $name;
    protected $price;

    public function __construct($sku, $name, $price) {
        $this->setSku($sku);
        $this->setName($name);
        $this->setPrice($price);
    }

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

    public function setSku($sku) {
        $this->sku = $sku;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    abstract public function save();
}
