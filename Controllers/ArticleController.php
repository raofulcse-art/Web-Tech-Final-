<?php
require_once __DIR__ . "/../models/Article.php";
require_once __DIR__ . "/../models/Category.php";

class ArticleController {

    public function create() {
        $categories = Category::all();
        require __DIR__ . "/../views/articles/create.php";
    }

    public function store() {

        

        Article::create([
            "category_id" => $_POST['category_id'],
            "title" => $_POST['title'],
            "body" => $_POST['body'],
            "status" => $_POST['status'],
            "publish_at" => $_POST['publish_at']
        ]);

        header("Location: index.php?action=dashboard");
        exit;
    }

    public function dashboard() {
        $articles = Article::allByAuthor(1);
        require __DIR__ . "/../views/articles/dashboard.php";
    }

    public function toggleStatus() {
        $id = $_POST['id'];
        $status = $_POST['status'];

        Article::toggleStatus($id, $status);

        echo json_encode(["success" => true]);
    }
}