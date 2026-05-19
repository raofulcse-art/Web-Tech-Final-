<?php
require_once __DIR__ . '/../config/database.php';

class Like {

    public static function toggle($article_id, $user_id) {
        $pdo = getDB();

        $stmt = $pdo->prepare("SELECT id FROM likes WHERE article_id = ? AND user_id = ?");
        $stmt->execute([$article_id, $user_id]);
        $existing = $stmt->fetch();

        if ($existing) {
            
            $pdo->prepare("DELETE FROM likes WHERE article_id = ? AND user_id = ?")
                ->execute([$article_id, $user_id]);
            $liked = false;
        } else {
         
            $pdo->prepare("INSERT INTO likes (article_id, user_id, created_at) VALUES (?, ?, NOW())")
                ->execute([$article_id, $user_id]);
            $liked = true;
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE article_id = ?");
        $stmt->execute([$article_id]);
        $count = (int) $stmt->fetchColumn();

        return ['liked' => $liked, 'count' => $count];
    }

    public static function hasLiked($article_id, $user_id) {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id FROM likes WHERE article_id = ? AND user_id = ?");
        $stmt->execute([$article_id, $user_id]);
        return (bool) $stmt->fetch();
    }
}
