<?php

namespace Models;

class Products
{
    private \Database $databaseObj;

    public function __construct()
    {
        $this->databaseObj = new \Database();
    }

    /**
     * return all the products from the database
     * @return array
     */
    public function getProducts(): array
    {
        $this->databaseObj->query('SELECT * FROM products LIMIT 10');
        return $this->databaseObj->resultSet();
    }
}
