<?php
// controllers/BlogController.php

$posts = [];
if (isset($conn)) {
    try {
        $stmt_posts = $conn->prepare("SELECT * FROM posts WHERE is_active = 1 ORDER BY created_at DESC");
        $stmt_posts->execute();
        $raw_posts = $stmt_posts->fetchAll();

        foreach ($raw_posts as $p) {
            $excerpt = mb_substr(strip_tags($p['content']), 0, 120) . '...';
            $posts[] = [
                'id' => $p['id'],
                'title' => $p['title'],
                'excerpt' => $excerpt,
                'image' => $p['image'],
                'date' => date('d/m/Y', strtotime($p['created_at'])),
                'category' => $p['category'] ?? 'Tin tức'
            ];
        }

    } catch (PDOException $e) {
        // Handle error
    }
}
?>