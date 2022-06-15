<?php

class Products extends Controller
{
    public Models\Products $productModel;

    public function __construct()
    {
        $this->productModel = $this->model('Products');
    }

    public function index()
    {
        $data = [
            'title' => 'Products',
            'products' => $this->productModel->getProducts()
        ];

        $this->view('products/index', $data);
    }
}
