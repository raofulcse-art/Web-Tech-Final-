<?php

class User {

    public $conn;

    public function __construct($db){
        $this->conn = $db;
    }

   
    public function register($name,$email,$pass,$role){

        $check = $this->conn->query("SELECT * FROM users WHERE email='$email'");

        if($check->rowCount() > 0){
            return false;
        }

        $pending = 0;

        if($role == "author"){
            $pending = 1;
            $role = "reader";
        }

        $hash = password_hash($pass,PASSWORD_DEFAULT);

        return $this->conn->query("
            INSERT INTO users(name,email,password_hash,role,pending_author)
            VALUES('$name','$email','$hash','$role','$pending')
        ");
    }

  
    public function login($email){
        
        return $this->conn->query("
            SELECT * FROM users WHERE email='$email'
        ");
    }

  
    public function getById($id){

        return $this->conn->query("
            SELECT * FROM users WHERE id=$id
        ")->fetch(PDO::FETCH_ASSOC);
    }

   
    public function updateProfile($id,$bio,$facebook,$img){

        return $this->conn->query("
            UPDATE users SET
            bio='$bio',
            social_links='$facebook',
            profile_pic_path='$img'
            WHERE id=$id
        ");
    }

    
    public function getAll(){

        return $this->conn->query("SELECT * FROM users");
    }

  
    public function promote($id){
        return $this->conn->query("
            UPDATE users SET role='author', pending_author=0
            WHERE id=$id
        ");
    }


    public function saveToken($id,$token){
        return $this->conn->query("
            UPDATE users SET remember_token='$token'
            WHERE id=$id
        ");
    }

    public function getByToken($token){
        return $this->conn->query("
            SELECT * FROM users WHERE remember_token='$token'
        ");


    }
}