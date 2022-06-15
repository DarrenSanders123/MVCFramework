<?php
    //Load the model and the view
    class Controller {
        /**
         * load a model
         * @param $model
         * @return mixed
         */
        public function model($model) {
            //Require model file
            require_once '../app/models/' . $model . '.php';
            //Instantiate model
            return new $model();
        }

        //Load the view (checks for the file)

        /**
         * @param $view
         * @param $data
         * @return void
         * display a view
         */
        public function view($view, $data = []) {
            if (file_exists('../app/views/' . $view . '.php')) {
                require_once '../app/views/' . $view . '.php';
            } else {
                die("View does not exists.");
            }
        }
    }
