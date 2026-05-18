<?php

class User {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    
    public function register($name,$email,$password,$role){

        $check = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);

        if($check->rowCount() > 0) return false;

        $pending = 0;

        if($role == "author"){
            $role = "reader";
            $pending = 1;
        }

        $hash = password_hash($password,PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO users(name,email,password_hash,role,pending_author)
            VALUES(?,?,?,?,?)
        ");

        return $stmt->execute([$name,$email,$hash,$role,$pending]);
    }

   
    public function login($email){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function updateProfile($id,$bio,$social,$img){
        $stmt = $this->db->prepare("
            UPDATE users SET bio=?, social_links=?, profile_pic_path=? WHERE id=?
        ");
        return $stmt->execute([$bio,$social,$img,$id]);
    }

   
    public function getAll(){
        return $this->db->query("SELECT * FROM users");
    }

   
    public function promote($id){
        $stmt = $this->db->prepare("
            UPDATE users SET role='author', pending_author=0 WHERE id=?
        ");
        return $stmt->execute([$id]);
    }

    
    public function saveToken($id,$token){
        $hash = password_hash($token,PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET remember_token=? WHERE id=?");
        return $stmt->execute([$hash,$id]);
    }

    public function findByToken($token){
        $users = $this->db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

        foreach($users as $u){
            if(password_verify($token,$u['remember_token'])){
                return $u;
            }
        }
        return false;
    }
}