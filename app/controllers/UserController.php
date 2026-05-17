<?php

require_once "../core/Database.php";
require_once "../app/models/User.php";

class UserController {

    public $model;

    public function __construct(){
        $db = new Database();
        $this->model = new User($db->connect());
    }

    
    public function register(){

        if(isset($_POST['submit'])){

            if(strlen($_POST['password']) < 8){
                header("Location: register.php?error=pass");
                exit;
            }

            $ok = $this->model->register(
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role']
            );

            if(!$ok){
                header("Location: register.php?error=email");
                exit;
            }

            header("Location: login.php");
            exit;
        }

        include __DIR__."/../views/users/register.php";
    }


    public function login(){

        session_start();

        if(isset($_POST['submit']))
            {

            $res = $this->model->login($_POST['email']);

            $user = $res->fetch(PDO::FETCH_ASSOC);

            if($user && password_verify($_POST['password'],$user['password_hash'])){

             $_SESSION['user_id']=$user['id'];
                $_SESSION['name']=$user['name'];
                $_SESSION['role']=$user['role'];

                if(isset($_POST['remember'])){
                    $token = bin2hex(random_bytes(16));
                    $this->model->saveToken($user['id'],$token);

                    setcookie("remember_me",$token,time()+86400*30,"/");
                }

                header("Location: profile.php");
                exit;
            }

            header("Location: login.php?error=1");
            exit;
        }

        include __DIR__."/../views/users/login.php";
    }

    public function profile()
    {

        session_start();

        if(!isset($_SESSION['user_id']))
            
            {
            header("Location: login.php");
            exit;
        }

      
        $user = $this->model->getById($_SESSION['user_id']);

        if(isset($_POST['submit'])){

            $img = $user['profile_pic_path'];

            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != ""){

                $img = time().$_FILES['image']['name'];

                move_uploaded_file(
                    $_FILES['image']['tmp_name'],   
                    "../public/uploads/avatars/".$img      
                );
             
            }

            $facebook = json_encode([
                "facebook" => $_POST['facebook']
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

        include __DIR__."/../views/users/profile.php";
    }

    
    public function users(){

        session_start();

        $users = $this->model->getAll();

        include __DIR__."/../views/users/users.php";
    }

  
    public function admin(){

        if(isset($_POST['user_id'])){

            $this->model->promote($_POST['user_id']);
        }

        header("Location: users.php");
        exit;
    }



    
}