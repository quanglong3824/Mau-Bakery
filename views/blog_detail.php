<?php
require_once 'controllers/BlogDetailController.php';

if (!$post) {
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

            <?php if (!empty($post['image'])): ?>
                <div
                    style="margin-bottom: 40px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" style="width: 100%; display: block;"
                        alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
            <?php endif; ?>

            <div class="post-content" style="font-size: 1.1rem; line-height: 1.8; color: #4a4a4a;">
                <?php echo $post['content']; // Content is usually HTML safe from editor, or use purifier ?>
            </div>

            <div
                style="margin-top: 50px; padding-top: 30px; border-top: 1px dashed #ccc; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600;">Chia sẻ bài viết:</span>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-glass" onclick="shareSocial('Facebook')"
                        style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; color: #3b5998;"><i
                            class="fab fa-facebook-f"></i></button>
                    <button class="btn-glass" onclick="shareSocial('Twitter')"
                        style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; color: #1da1f2;"><i
                            class="fab fa-twitter"></i></button>
                    <button class="btn-glass" onclick="shareSocial('Instagram')"
                        style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; color: #c32aa3;"><i
                            class="fab fa-instagram"></i></button>
                </div>
            </div>
        </article>

    </div>
</div>

<link rel="stylesheet" href="assets/css/blog_detail.css">
<script src="assets/js/blog_detail.js"></script>