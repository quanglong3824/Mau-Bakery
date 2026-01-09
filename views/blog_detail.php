<?php
// views/blog_detail.php

// Get ID or Slug
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$post = null;

if (isset($conn) && $id > 0) {
    // Determine table - assuming 'posts' or 'blogs'
    // Checking schema from previous context or generic assumption.
    // Let's assume 'posts' table exists. If not, I'll fallback to a generic message.
    try {
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id AND status = 'published'");
        $stmt->execute(['id' => $id]);
        $post_data = $stmt->fetch();

        if ($post_data) {
             // Fetch author name if author_id exists, OR just use 'Admin'
             $author = 'Admin';
             if(isset($post_data['author_id'])) {
                 $stmt_author = $conn->prepare("SELECT username FROM users WHERE id = :uid");
                 $stmt_author->execute(['uid' => $post_data['author_id']]);
                 $u = $stmt_author->fetch();
                 if($u) $author = $u['username'];
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

if (!$post) {
     // Fallback if no DB content found to avoid broken page during demo
     echo "<div class='container mt-2'><div class='glass-panel' style='padding:40px; text-align:center'><h3>Bài viết không tồn tại!</h3><a href='index.php?page=blog' class='btn-glass mt-1'>Quay lại Blog</a></div></div>";
    return;
}
?>

<div class="container mt-2 mb-2">
    <div style="max-width: 800px; margin: 0 auto;">

        <!-- Breadcrumb -->
        <div style="margin-bottom: 20px; font-size: 0.9rem;">
            <a href="index.php?page=blog" style="color: #888;">Blog</a>
            <i class="fas fa-chevron-right" style="font-size: 0.7rem; margin: 0 10px; color: #ccc;"></i>
            <span style="color: var(--accent-color);">
                <?php echo htmlspecialchars($post['category']); ?>
            </span>
        </div>

        <article class="glass-panel" style="padding: 40px;">
            <header style="margin-bottom: 30px; text-align: center;">
                <div style="color: var(--accent-color); font-weight: 600; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($post['category']); ?>
                </div>
                <h1
                    style="font-family: 'Quicksand', sans-serif; font-size: 2.2rem; margin-bottom: 20px; line-height: 1.3;">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
                <div style="color: #888; font-size: 0.9rem;">
                    <span><i class="far fa-user"></i>
                        <?php echo htmlspecialchars($post['author']); ?>
                    </span>
                    <span style="margin: 0 15px;">|</span>
                    <span><i class="far fa-calendar"></i>
                        <?php echo $post['date']; ?>
                    </span>
                </div>
            </header>

            <?php if(!empty($post['image'])): ?>
            <div
                style="margin-bottom: 40px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <img src="<?php echo htmlspecialchars($post['image']); ?>" style="width: 100%; display: block;" alt="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
            <?php endif; ?>

            <div class="post-content" style="font-size: 1.1rem; line-height: 1.8; color: #4a4a4a;">
                <?php echo $post['content']; // Content is usually HTML safe from editor, or use purifier ?>
            </div>

            <div
                style="margin-top: 50px; padding-top: 30px; border-top: 1px dashed #ccc; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600;">Chia sẻ bài viết:</span>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-glass"
                        style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; color: #3b5998;"><i
                            class="fab fa-facebook-f"></i></button>
                    <button class="btn-glass"
                        style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; color: #1da1f2;"><i
                            class="fab fa-twitter"></i></button>
                    <button class="btn-glass"
                        style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; color: #c32aa3;"><i
                            class="fab fa-instagram"></i></button>
                </div>
            </div>
        </article>

    </div>
</div>

<style>
    .post-content h4 {
        font-family: 'Quicksand', sans-serif;
        font-size: 1.4rem;
        margin-top: 30px;
        margin-bottom: 15px;
        color: var(--text-color);
    }

    .post-content ul {
        margin-bottom: 20px;
        padding-left: 20px;
    }

    .post-content li {
        margin-bottom: 10px;
    }
    .post-content img {
        max-width: 100%;
        border-radius: 10px;
        margin: 20px 0;
    }
</style>