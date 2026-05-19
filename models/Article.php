<?php
require_once __DIR__ . '/../config/database.php';

class Article {
    public static function publishScheduled() {
        $pdo = getDB();
        $pdo->prepare("
            UPDATE articles
            SET status = 'published'
            WHERE status = 'draft'
              AND publish_at IS NOT NULL
              AND publish_at <= NOW()
        ")->execute();
    }

    public static function getPublished($category_id = null) {
        $pdo = getDB();

        $base = "
            SELECT
                a.id, a.title, a.featured_image_path,
                a.view_count, a.created_at, a.author_id,
                u.name  AS author_name,
                u.profile_pic_path,
                c.name  AS category_name,
                c.id    AS category_id,
                COUNT(l.id) AS like_count
            FROM articles a
            JOIN  users      u ON a.author_id   = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN likes      l ON a.id       = l.article_id
            WHERE a.status = 'published'
        ";

        if ($category_id) {
            $stmt = $pdo->prepare($base . " AND a.category_id = ? GROUP BY a.id ORDER BY a.created_at DESC");
            $stmt->execute([$category_id]);
        } else {
            $stmt = $pdo->prepare($base . " GROUP BY a.id ORDER BY a.created_at DESC");
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }
    public static function getById($id) {
        $pdo  = getDB();
        $stmt = $pdo->prepare("
            SELECT
                a.*,
                u.name  AS author_name,
                u.profile_pic_path,
                u.bio   AS author_bio,
                c.name  AS category_name,
                COUNT(l.id) AS like_count
            FROM articles a
            JOIN  users      u ON a.author_id   = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN likes      l ON a.id       = l.article_id
            WHERE a.id = ?
            GROUP BY a.id
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function getTags($article_id) {
        $pdo  = getDB();
        $stmt = $pdo->prepare("
            SELECT t.name
            FROM tags t
            JOIN article_tags at ON t.id = at.tag_id
            WHERE at.article_id = ?
        ");
        $stmt->execute([$article_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function incrementViews($id) {
        $pdo = getDB();
        $pdo->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?")
            ->execute([$id]);
    }


    public static function search($query) {
        $pdo = getDB();
        $q   = '%' . trim($query) . '%';
        $stmt = $pdo->prepare("
            SELECT DISTINCT
                a.id, a.title,
                a.featured_image_path,
                u.name AS author_name
            FROM articles a
            JOIN users u ON a.author_id = u.id
            LEFT JOIN article_tags at ON a.id = at.article_id
            LEFT JOIN tags t ON at.tag_id = t.id
            WHERE a.status = 'published'
              AND (a.title LIKE ? OR t.name LIKE ?)
            LIMIT 8
        ");
        $stmt->execute([$q, $q]);
        return $stmt->fetchAll();
    }

    public static function getCategories() {
        $pdo  = getDB();
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }
}
