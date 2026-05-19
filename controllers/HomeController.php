<?php
session_start();
require_once __DIR__ . '/../models/Article.php';

Article::publishScheduled();

$categories = Article::getCategories();
$articles   = Article::getPublished();

require_once __DIR__ . '/../views/home.php';
