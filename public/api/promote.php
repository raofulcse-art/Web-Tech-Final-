<?php
header("Content-Type: application/json");

require "../../core/Database.php";
require "../../app/models/User.php";

$db = new Database();
$model = new User($db->connect());

if($_POST['user_id']){
    $model->promote($_POST['user_id']);

    echo json_encode(["status"=>"success"]);
}