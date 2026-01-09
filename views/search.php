<?php
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$results = []; // Mock empty or filled based on logic
?>

<div class="container mt-2 mb-2">
    <div class="glass-panel" style="margin-bottom: 30px; text-align: center;">
        <h1 style="font-family: 'Quicksand', sans-serif; margin-bottom: 20px;">Tìm kiếm</h1>
        <form action="index.php" method="GET" style="max-width: 500px; margin: 0 auto; position: relative;">
            <input type="hidden" name="page" value="search">
            <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>"
                placeholder="Bạn muốn tìm bánh gì hôm nay?..."
                style="width: 100%; padding: 15px 20px 15px 50px; border-radius: 30px; border: 1px solid rgba(0,0,0,0.1); font-size: 1rem; outline: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
            <button type="submit"
                style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.2rem; color: #888; cursor: pointer;">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <?php if ($keyword): ?>
        <p style="margin-bottom: 20px; color: #666;">Kết quả tìm kiếm cho: <strong>"
                <?php echo htmlspecialchars($keyword); ?>"
            </strong></p>

        <!-- Mock functionality: Show no results for now -->
        <div class="text-center" style="padding: 50px; color: #888;">
            <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
            <p>Hãy thử từ khóa khác như "Kem", "Dâu", "Sinh nhật"...</p>
        </div>
    <?php endif; ?>
</div>