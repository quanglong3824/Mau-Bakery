// Simple Remove Logic
document.querySelectorAll('.btn-remove-fav').forEach(btn => {
    btn.addEventListener('click', function() {
        if(!confirm('Bỏ sản phẩm này khỏi danh sách yêu thích?')) return;
        
        const productId = this.getAttribute('data-id');
        const card = this.closest('.product-card');
        
        // Call API to remove
        fetch('api/toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'removed') {
                card.remove();
                // Reload if empty
                if(document.querySelectorAll('.product-card').length === 0) location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(err => {
            // Fallback for demo if API not exists yet
            card.style.display = 'none';
            alert('Đã xóa (Demo)');
        });
    });
});
