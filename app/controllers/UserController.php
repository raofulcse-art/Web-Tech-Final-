<?php

require_once "../core/Database.php";
require_once "../app/models/User.php";

class UserController {

    public $model;

    public function __construct(){
        $db = new Database();
        $this->model = new User($db->connect());
    }

    // REGISTER
    public function register(){

        if(isset($_POST['submit'])){
            $this->model->register($_POST['name'],$_POST['email'],$_POST['password'],$_POST['role']);
            header("Location: login.php?success=1");
            exit;
        }

        include "../app/views/users/register.php";
    }

    // LOGIN
    public function login(){

        session_start();

        if(isset($_POST['submit'])){

            $res = $this->model->login($_POST['email']);
            $user = $res->fetch(PDO::FETCH_ASSOC);

            if($user && password_verify($_POST['password'],$user['password_hash'])){

                $_SESSION['user_id']=$user['id'];
                $_SESSION['name']=$user['name'];
                $_SESSION['role']=$user['role'];

                header("Location: profile.php?login=success");
                exit;
            }

            header("Location: login.php?error=1");
            exit;
        }

        include "../app/views/users/login.php";
    }

    // PROFILE
    public function profile(){

        session_start();

        if(!isset($_SESSION['user_id'])){
            header("Location: login.php");
            exit;
        }

        $user = $this->model->getById($_SESSION['user_id']);

        if(isset($_POST['submit'])){

            $img = $user['profile_pic_path'];

            if(isset($_FILES['image']['name']) && $_FILES['image']['name']!=""){
                $img = time().$_FILES['image']['name'];

                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    "../public/uploads/avatars/".$img
                );
            }

            $facebook = json_encode([
                "facebook"=>$_POST['facebook']
            ]);

            $this->model->updateProfile(
                $_SESSION['user_id'],
                $_POST['bio'],
                $facebook,
                $img
            );

            header("Location: profile.php?updated=1");
            exit;
        }

        include "../app/views/users/profile.php";
    }

    // USERS (ADMIN)
    public function users(){

        session_start();

        $users = $this->model->getAll();

        include "../app/views/users/users.php";
    }

    // PROMOTE
    public function admin(){

        if(isset($_POST['user_id'])){
            $this->model->promote($_POST['user_id']);
        }

        header("Location: users.php");
        exit;
    }
}