<?php

session_start();

require_once "../core/Database.php";
require_once "../app/models/User.php";

$db = new Database();
$model = new User($db->connect());

if(isset($_COOKIE['remember_me'])){

    $token = $_COOKIE['remember_me'];

    $res = $model->getByToken($token);
    $user = $res->fetch(PDO::FETCH_ASSOC);

    if($user){

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header("Location: profile.php");
        exit;
    }
}