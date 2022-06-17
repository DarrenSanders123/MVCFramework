<?php

/**
 * Default controller
 */
class Pages extends Controller
{
    public function __construct()
    {
        //$this->userModel = $this->model('User');
    }

    /**
     * index of the home page
     * @return void
     */
    public function index(): void
    {
        $data = [
            'title' => 'Home page'
        ];

        $this->view('index', $data);
    }
}
