<?php

require_once "../core/Database.php";
require_once "../app/models/User.php";

class UserController {

    private $model;

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

            if($ok){
                header("Location: login.php?success=1");
            } else {
                header("Location: register.php?error=email");
            }
            exit;
        }

        include "../app/views/users/register.php";
    }

   
    public function login(){

        session_start();

        if(isset($_POST['submit'])){

            $user = $this->model->login($_POST['email']);

            if($user && password_verify($_POST['password'],$user['password_hash'])){

                $_SESSION['user_id']=$user['id'];
                $_SESSION['name']=$user['name'];
                $_SESSION['role']=$user['role'];

                // remember me
                if(isset($_POST['remember'])){

                    $token = bin2hex(random_bytes(16));

                    $this->model->saveToken($user['id'],$token);

                    setcookie("remember_me",$token,time()+60*60*24*30,"/");
                }

                header("Location: profile.php?login=success");
                exit;
            }

            header("Location: login.php?error=1");
            exit;
        }

        include "../app/views/users/login.php";
    }

   
    public function profile(){

        session_start();

        if(!isset($_SESSION['user_id'])){
            header("Location: login.php");
            exit;
        }

        $user = $this->model->getById($_SESSION['user_id']);

        if(isset($_POST['submit'])){

            $img = $user['profile_pic_path'];

            if(!empty($_FILES['image']['name'])){

                $ext = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);

                if(!in_array($ext,['jpg','jpeg','png'])){
                    die("Invalid file type");
                }

                if($_FILES['image']['size'] > 1000000){
                    die("File too large");
                }

                $img = time().$_FILES['image']['name'];

                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    "../public/uploads/avatars/".$img
                );
            }

            $social = json_encode([
                "facebook"=>$_POST['facebook']
            ]);

            $this->model->updateProfile(
                $_SESSION['user_id'],
                $_POST['bio'],
                $social,
                $img
            );

            header("Location: profile.php?updated=1");
            exit;
        }

        include "../app/views/users/profile.php";
    }

  
    public function users(){

        session_start();

        $users = $this->model->getAll();

        include "../app/views/users/users.php";
    }

    
    public function admin(){

        if(isset($_POST['user_id'])){
            $this->model->promote($_POST['user_id']);
        }

        header("Location: users.php");
        exit;
    }

   
    public function author(){

        $id = $_GET['id'];

        $user = $this->model->getById($id);

        if(!$user){
            die("Author not found");
        }

        include "../app/views/users/author.php";
    }
}