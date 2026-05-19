<?php

require_once __DIR__ . "/../controllers/ArticleController.php";
require_once __DIR__ . "/../controllers/CategoryController.php";

$action = $_GET['action'] ?? 'dashboard';

$article = new ArticleController();
$category = new CategoryController();

switch ($action) {

    case "create":
        $article->create();
        break;

    case "store":
        $article->store();
        break;

    case "dashboard":
        $article->dashboard();
        break;

    case "toggle":
        $article->toggleStatus();
        break;

    case "category_store":
        $category->store();
        break;

    case "category_all":
        $category->all();
        break;
}