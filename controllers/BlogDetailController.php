<?php
// controllers/BlogDetailController.php

// Get ID or Slug
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$post = null;

if (isset($conn) && $id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id AND status = 'published'");
        $stmt->execute(['id' => $id]);
        $post_data = $stmt->fetch();

        if ($post_data) {
            // Fetch author name if author_id exists, OR just use 'Admin'
            $author = 'Admin';
            if (isset($post_data['author_id'])) {
                $stmt_author = $conn->prepare("SELECT username FROM users WHERE id = :uid");
                $stmt_author->execute(['uid' => $post_data['author_id']]);
                $u = $stmt_author->fetch();
                if ($u)
                    $author = $u['username'];
            }

            $post = [
                'id' => $post_data['id'],
                'title' => $post_data['title'],
                'content' => $post_data['content'],
                'image' => $post_data['image'] ?? 'assets/images/blog-default.jpg',
                'date' => date('d/m/Y', strtotime($post_data['created_at'])),
                'author' => $author,
                'category' => 'Tin tức' // Or fetch category
            ];
        }
    } catch (Exception $e) {
        // Table might not exist yet
    }
}
?>