<?php

class User {

    private $db;

    public function __construct($conn){
        $this->db = $conn;
    }

    public function register($name,$email,$password,$role,$pending){

        $hash = password_hash($password,PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
            INSERT INTO users(name,email,password_hash,role,pending_author)
            VALUES(?,?,?,?,?)
        ");

        return $stmt->execute([$name,$email,$hash,$role,$pending]);
    }

  
    public function login($email){

        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE email=?
        ");

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function getById($id){

        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE id=?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id,$bio,$social,$image){

        $stmt = $this->db->prepare("
            UPDATE users 
            SET bio=?, social_links=?, profile_pic_path=?
            WHERE id=?
        ");

        return $stmt->execute([$bio,$social,$image,$id]);
    }

    
    public function getAll(){

        $stmt = $this->db->query("
            SELECT * FROM users
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function promote($id){

        $stmt = $this->db->prepare("
            UPDATE users 
            SET role='author', pending_author=0
            WHERE id=?
        ");

        return $stmt->execute([$id]);
    }

    public function saveToken($id,$token){

        $hash = password_hash($token,PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
            UPDATE users 
            SET remember_token=?
            WHERE id=?
        ");

        return $stmt->execute([$hash,$id]);
    }


    public function findByToken($token){

        $stmt = $this->db->query("
            SELECT * FROM users
        ");

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($users as $user){
            if(password_verify($token,$user['remember_token'])){
                return $user;
            }
        }

        return false;
    }
}