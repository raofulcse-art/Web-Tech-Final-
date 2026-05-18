<?php
session_start();

require "../core/Database.php";
require "../app/models/User.php";

$db = new Database();
$model = new User($db->connect());

if(!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])){

    $user = $model->findByToken($_COOKIE['remember_me']);

    if($user){
        $_SESSION['user_id']=$user['id'];
        $_SESSION['name']=$user['name'];
        $_SESSION['role']=$user['role'];
    }
}