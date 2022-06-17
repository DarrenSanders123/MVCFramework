<?php

/**
 * Controller to manage the product pages
 */
class Products extends Controller
{
    public mixed $productModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productModel = $this->model('ProductsModel');
    }

    /**
     * Index page
     * @return void
     */
    public function index(): void
    {
        $productsFormatted = "";
        $products = $this->productModel->getProducts($_GET['page'] ?? '1');

        $productsFormatted .= "<table>";
        $productsFormatted .= "<thead>
                <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Update</th>
                        <th>Delete</th>
                </tr>";
        $productsFormatted .= "</thead><tbody>";
        foreach ($products as $product) {
            $productsFormatted .= "<tr>";
            $productsFormatted .= "<td>" . $product->ProductId . "</td>";
            $productsFormatted .= "<td>" . $product->ProductName . "</td>";
            $productsFormatted .= "<td>" . $product->Price . "</td>";
            $productsFormatted .= "<td>" . $product->Category . "</td>";
            $productsFormatted .= "<td><button><a href='" . URL_ROOT . "/products/update/" . $product->ProductId . "'>Update</a></button></td>";
            $productsFormatted .= "<td><button><a href='" . URL_ROOT . "/products/delete/" . $product->ProductId . "'>Delete</a></button></td>";
            $productsFormatted .= "<tr>";
        }

        $productsFormatted .= "</tbody></table>";

        $data = [
            'title' => 'Products',
            'products' => $productsFormatted
        ];

        $this->view('products/index', $data);
    }

    /**
     * Create a new product
     * @return void
     */
    public function create(): void
    {
        $data = [
            'categories' => $this->productModel->getCategories()
        ];

        if (!empty($_POST)) {
            $formattedCategory = [];

            foreach ($this->productModel->getCategories() as $category) {
                $formattedCategory[] = $category->CategoryId;
            }

            $productName = getVal('product_create_')
                ->type('string')
                ->condition("minlen", "Product name has to be at least 3 characters long.", 3)
                ->condition("maxlen", "Product name cant be longer then 300", 300)
                ->post('productName');

            $price = getVal('product_create_')
                ->type('float')
                ->condition("req", "This field is required.")
                ->condition("gte", "Price has to be at least 0.01", 0.01)
                ->post('price');

            $category = getVal('product_create_')
                ->type('string')
                ->condition("eq", "Category is invalid.", array_values($formattedCategory))
                ->post('category');

            $data += ['productName' => $productName];
            $data += ['price' => $price];
            $data += ['category' => $category];

            if (getVal('product_create_')->messageList->errorCount === 0) {
                if ($this->productModel->createProduct($data)) {
                    header("LOCATION: /products");
                } else {
                    getLog('db')->error('Something went wrong creating a product.');
                }
            }
        }

        $this->view('products/create', $data);
    }

    /**
     * Update a product
     * @param $id
     * @return void
     */
    public function update($id): void
    {
        $data = [
            'categories' => $this->productModel->getCategories()
        ];

        if (!empty($_POST)) {
            $formattedCategory = [];

            foreach ($this->productModel->getCategories() as $category) {
                $formattedCategory[] = $category->CategoryId;
            }

            $productName = getVal('product_update_')
                ->type('string')
                ->condition("minlen", "Product name has to be at least 3 characters long.", 3)
                ->condition("maxlen", "Product name cant be longer then 300", 300)
                ->post('productName');

            $price = getVal('product_update_')
                ->type('float')
                ->condition("req", "This field is required.")
                ->condition("gte", "Price has to be at least 0.01", 0.01)
                ->post('price');

            $category = getVal('product_update_')
                ->type('string')
                ->condition("eq", "Category is invalid.", array_values($formattedCategory))
                ->post('category');

            $productId = getVal('product_update_')
                ->type('string')
                ->post('productId');

            $data += ['productName' => $productName];
            $data += ['productId' => $productId];
            $data += ['price' => $price];
            $data += ['category' => $category];

            if (getVal('product_update_')->messageList->errorCount === 0) {
                if ($this->productModel->updateProduct($data)) {
                    header("LOCATION: /products");
                } else {
                    getLog('db')->error('Something went wrong updating a product.');
                }
            }
        } else {
            $product = $this->productModel->getProduct($id);

            $data += ['productName' => $product->ProductName];
            $data += ['productId' => $product->ProductId];
            $data += ['price' => $product->Price];
            $data += ['category' => $product->Category];
        }

        $this->view('products/update', $data);
    }

    /**
     * Delete a product
     * @param $id
     * @return void
     */
    public function delete($id): void
    {
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            header('LOCATION: /products');
        }
    }

}