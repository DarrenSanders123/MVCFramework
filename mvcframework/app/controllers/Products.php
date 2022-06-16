<?php

use eftec\ValidationOne;

/**
 * Controller to manage the product pages
 */
class Products extends Controller
{
    public mixed $productModel;

    public function __construct()
    {
        $this->productModel = $this->model('ProductsModel');
    }

    public function index()
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

    public function create()
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
                    getVal('db_')->messageList->addItem('database', 'Something went wrong creating a product.', 'error');
                }
            }
        }

        $this->view('products/create', $data);
    }

    public function delete($id)
    {
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            header('LOCATION: /products');
        }
    }

}