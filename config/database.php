<?php
function getDB() {
    $host = 'localhost';
    $db   = 'blog_platform';
    $user = 'root';
    $pass = '';  // XAMPP default has no password

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8mb4",
            $user,
            $pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die(json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]));
    }
}
