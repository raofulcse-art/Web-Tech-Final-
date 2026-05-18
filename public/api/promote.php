<?php

header("Content-Type: application/json");

require_once "../../core/Database.php";
require_once "../../app/models/User.php";

$db = new Database();
$model = new User($db->connect());

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

if(!isset($_POST['user_id'])){
    echo json_encode([
        "status" => "error",
        "message" => "User ID missing"
    ]);
    exit;
}

$user_id = $_POST['user_id'];

try{

    $result = $model->promote($user_id);

    if($result){

        echo json_encode([
            "status" => "success",
            "message" => "User promoted to author"
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Promotion failed"
        ]);
    }

}catch(Exception $e){

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}