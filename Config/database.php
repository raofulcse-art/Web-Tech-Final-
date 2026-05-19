<?php

class Database {
    public static function connect() {
        $host = "127.0.0.1";
        $db   = "article_db";
        $user = "root";
        $pass = "";

        try {
            return new PDO(
                "mysql:host=$host;dbname=$db;charset=utf8",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (PDOException $e) {
            die("DB Connection Failed: " . $e->getMessage());
        }
    }
}