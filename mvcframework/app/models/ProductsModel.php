<?php

/**
 * Model to talk with the database for products
 */
class ProductsModel
{
    private mixed $databaseObj;

    public function __construct()
    {
        $this->databaseObj = new \Database();
    }

    /**
     * return all the products from the database
     * @return array
     */
    public function getProducts($page): array
    {
        $min = $page - 1;
        $max = $page * 10;
        $this->databaseObj->query('
        SELECT 
            products.ProductId,
            products.ProductName,
            p.Price,
            c.CategoryName as Category
        FROM products
            INNER JOIN price p on ProductPriceId = p.PriceId
            LEFT JOIN category c on ProductCategoryId = c.CategoryId 
        LIMIT :min, :max
        ');
        $this->databaseObj->bind(':min', $min);
        $this->databaseObj->bind(':max', $max);

        return $this->databaseObj->resultSet();
    }

    public function updateProduct($data): string|bool
    {

    }

    /**
     * delete the product and price connected with it with the given id
     * @param $id
     * @return bool
     */
    public function deleteProduct($id): bool
    {
        try {
            // geef mij het price id van de product die ik ga verwijderen.
            $this->databaseObj->query("SELECT ProductPriceId FROM products WHERE ProductId = :id");
            $this->databaseObj->bind(':id', $id);

            $PriceId = $this->databaseObj->single()->ProductPriceId;

            $this->databaseObj->query('DELETE FROM products WHERE ProductId = :id');
            $this->databaseObj->bind(':id', $id);
            $this->databaseObj->execute();

            $this->databaseObj->query('DELETE FROM price WHERE PriceId = :id');
            $this->databaseObj->bind(':id', $PriceId);
            $this->databaseObj->execute();
            return true;
        } catch (PDOException $e) {
            die(print_r($e));
        }
    }
}