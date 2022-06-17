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

    public function getCategories(): array
    {
        try {
            $this->databaseObj->query('SELECT * FROM category');
            return $this->databaseObj->resultSet();
        } catch (PDOException $exception) {
            getLog('database')->error('Message: '. $exception->getMessage());
        }
    }

    public function getProduct($id): stdClass|bool
    {
        try {
            $this->databaseObj->query('
        SELECT 
            products.ProductId,
            products.ProductName,
            p.Price,
            c.CategoryId as Category
        FROM products
            INNER JOIN price p on ProductPriceId = p.PriceId
            INNER JOIN category c on ProductCategoryId = c.CategoryId 
        WHERE ProductId = :ProductId;
        ');
            $this->databaseObj->bind(':ProductId', $id);

            return $this->databaseObj->single();
        } catch (PDOException $exception) {
            getLog('db')->error('Message: ' . $exception->getMessage());
            return false;
        }
    }

    /**
     * return all the products from the database
     * @param $page
     * @return array|bool
     */
    public function getProducts($page): array|bool
    {
        try {
            $min = $page * 10 - 10;
            $max = $page * 10;
            $this->databaseObj->query('
        SELECT 
            products.ProductId,
            products.ProductName,
            p.Price,
            c.CategoryName as Category
        FROM products
            INNER JOIN price p on ProductPriceId = p.PriceId
            INNER JOIN category c on ProductCategoryId = c.CategoryId 
        LIMIT :min, :max
        ');
            $this->databaseObj->bind(':min', $min);
            $this->databaseObj->bind(':max', $max);

            return $this->databaseObj->resultSet();
        } catch (PDOException $exception) {
            getLog('db')->error('Message: ' . $exception->getMessage() . '\n in: ' . $exception->getFile());
            return false;
        }
    }

    /**
     * Update a product
     * @param $data
     * @return string|bool
     */
    public function updateProduct($data): string|bool
    {
        try {
            $this->databaseObj->query(/** @lang MySQL */ '
                START TRANSACTION;
                UPDATE products 
                    INNER JOIN price p on products.ProductPriceId = p.PriceId
                SET ProductName = :ProductName, ProductCategoryId = :CategoryId, p.Price = :Price
                    WHERE products.ProductId = :ProductId;
                COMMIT;
            ');

            $this->databaseObj->bind(':ProductName', $data['productName']);
            $this->databaseObj->bind(':ProductId', $data['productId']);
            $this->databaseObj->bind(':CategoryId', $data['category']);
            $this->databaseObj->bind(':Price', $data['price']);

            $this->databaseObj->execute();
            return true;
        } catch (PDOException $exception) {
            getLog('db')->error('Message: ' . $exception->getMessage());
            return false;
        }
    }

    /**
     * Create a product with the given data.
     * @param $data
     * @return bool
     */
    public function createProduct($data): bool
    {
        try {
            $this->databaseObj->query(/** @lang MySQL */ '
                START TRANSACTION; 
                INSERT INTO price VALUES (null, :Price, default, default);
                set @priceId := last_insert_id();

                INSERT INTO products VALUES (null, :ProductName, @priceId, :CategoryId, default, default);
                COMMIT;
        ');

            $this->databaseObj->bind(':Price', (float)$data['price']);
            $this->databaseObj->bind(':ProductName', $data['productName']);
            $this->databaseObj->bind(':CategoryId', (int)$data['category']);
            $this->databaseObj->execute();

            return true;
        } catch (PDOException $exception) {
            getLog('db')->error('Message: ' . $exception->getMessage() . '\n in: ' . $exception->getFile());
            return false;
        }
    }


    /**
     * delete the product and price connected with it with the given id
     * @param $id
     * @return bool
     */
    public function deleteProduct($id): bool
    {
        try {
            $this->databaseObj->query(/** @lang MySQL */ '
                    START TRANSACTION;
                    DELETE products, p
                        FROM products
                            INNER JOIN price p on products.ProductPriceId = p.PriceId
                        WHERE ProductId = :ProductId;
                    COMMIT;
                    ');

            $this->databaseObj->bind(':ProductId', $id);
            $this->databaseObj->execute();
            return true;
        } catch (PDOException $exception) {
            getLog('db')->error('Message: ' . $exception->getMessage());
            return false;
        }
    }
}