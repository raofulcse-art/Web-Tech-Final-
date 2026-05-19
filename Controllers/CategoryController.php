<?php
require_once __DIR__ . "/../models/Category.php";

class CategoryController {

    public function store() {
        Category::create($_POST['name']);
        echo json_encode(["success" => true]);
    }

    public function all() {
        echo json_encode(Category::all());
    }
}