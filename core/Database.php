<?php
require_once __DIR__."/../config/config.php";

class Database {
    public function connect(){
        try {
            return new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME,
                DB_USER,
                DB_PASS
            );
        } catch(Exception $e){
            
            die("Database is not connected!");
        }
    }
}