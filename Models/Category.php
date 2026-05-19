<?php
require_once __DIR__ . "/../core/Model.php";

class Category extends Model {

    public static function all() {
        $stmt = self::db()->query("SELECT * FROM categories ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($name) {
        $stmt = self::db()->prepare("INSERT INTO categories(name) VALUES(?)");
        return $stmt->execute([$name]);
    }
}