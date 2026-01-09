<?php
require_once 'controllers/BlogController.php';
?>

<div class="container mt-2 mb-2">
    <div class="text-center mb-2">
        <h1 class="section-title">Góc Ngọt Ngào</h1>
        <p>Chia sẻ kiến thức, mẹo vặt và những câu chuyện về bánh</p>
    </div>

    <div class="blog-grid"
        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
        <?php foreach ($posts as $post): ?>
            <article class="glass-panel"
                style="overflow: hidden; padding: 0; display: flex; flex-direction: column; transition: transform 0.3s;">
                <div style="height: 200px; overflow: hidden;">
                    <img src="<?php echo $post['image']; ?>"
                        style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;"
                        onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <div style="padding: 25px; flex: 1; display: flex; flex-direction: column;">
                    <div
                        style="margin-bottom: 10px; display: flex; justify-content: space-between; font-size: 0.85rem; color: #888;">
                        <span style="color: var(--accent-color); font-weight: 600;">
                            <?php echo $post['category']; ?>
                        </span>
                        <span>
                            <?php echo $post['date']; ?>
                        </span>
                    </div>
                    <h3 style="margin-bottom: 15px; font-size: 1.2rem;">
                        <a href="index.php?page=blog_detail&id=<?php echo $post['id']; ?>"
                            style="color: var(--text-color); text-decoration: none;">
                            <?php echo $post['title']; ?>
                        </a>
                    </h3>
                    <p style="color: #666; margin-bottom: 20px; font-size: 0.95rem; line-height: 1.6; flex: 1;">
                        <?php echo $post['excerpt']; ?>
                    </p>
                    <a href="index.php?page=blog_detail&id=<?php echo $post['id']; ?>"
                        style="color: var(--accent-color); font-weight: 600; display: inline-flex; align-items: center;">
                        Đọc tiếp <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 0.8rem;"></i>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 50px;">
        <a href="#" class="btn-glass btn-primary"
            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; padding: 0; border: none;">1</a>
        <a href="#" class="btn-glass"
            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; padding: 0;">2</a>
        <a href="#" class="btn-glass"
            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; padding: 0;">3</a>
    </div>
</div>