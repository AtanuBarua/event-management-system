<?php

namespace Core;

class Controller {
    public function view($view, $data = []) {
        require_once '../app/views/' . $view . '.php';
    }

    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    protected function render($view, $data = []) {
        extract($data);
        require_once "../views/$view.php";
    }
}
