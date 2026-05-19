<?php
require_once __DIR__ . "/../core/Model.php";

class Article extends Model {

    public static function create($data) {
        $sql = "INSERT INTO articles 
(author_id, category_id, title, body, status, publish_at, view_count, created_at)
VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";


        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            1, // fixed user_id
            $data['category_id'],
            $data['title'],
            $data['body'],
            $data['status'],
            $data['publish_at'],
            0
        ]);
    }

    public static function allByAuthor($author_id = 1) {
        $stmt = self::db()->prepare("SELECT * FROM articles WHERE author_id = ?");
        $stmt->execute([$author_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function toggleStatus($id, $status) {
        $stmt = self::db()->prepare("UPDATE articles SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}