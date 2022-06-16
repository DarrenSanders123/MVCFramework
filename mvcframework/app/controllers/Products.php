<?php
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

    public function index() {
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
            $productsFormatted .= "<td><button><a href='"  .  URL_ROOT . "/products/update/" . $product->ProductId . "'>Update</a></button></td>";
            $productsFormatted .= "<td><button><a href='"  .  URL_ROOT . "/products/delete/" . $product->ProductId . "'>Delete</a></button></td>";
            $productsFormatted .= "<tr>";
        }

        $productsFormatted .= "</tbody></table>";

        $data = [
            'title' => 'Products',
            'products' => $productsFormatted
        ];

        $this->view('products/index', $data);
    }

    public function delete($id) {
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            header('LOCATION: /products');
        }
    }

}