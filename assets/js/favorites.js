function toggleFavorite(event, productId) {
    event.stopPropagation(); // Prevent card click
    event.preventDefault();

    const btn = event.currentTarget;
    const icon = btn.querySelector('i');

    fetch('api/toggle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => {
        if (response.status === 401) {
            alert('Vui lòng đăng nhập để lưu sản phẩm yêu thích!');
            window.location.href = 'index.php?page=login'; // Or open modal
            throw new Error('Unauthorized');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Updated UI
            if (data.action === 'added') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                icon.style.color = '#e74c3c';
                
                // Optional: Add animation
                btn.style.transform = 'scale(1.2)';
                setTimeout(() => btn.style.transform = 'scale(1)', 200);
            } else {
                // If on favorites page, maybe remove the card?
                // For now just toggle icon
                icon.classList.remove('fas');
                icon.classList.add('far');
                icon.style.color = '#888';
                
                // If we are on the favorites page, remove the item
                const isFavoritesPage = window.location.search.includes('page=favorites');
                if (isFavoritesPage) {
                     const card = btn.closest('.product-card');
                     if(card) card.remove();
                     
                     // Check if empty
                     const grid = document.querySelector('.product-grid');
                     if(grid && grid.children.length === 0) {
                         grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #888; padding: 40px;">Chưa có sản phẩm yêu thích nào.</p>';
                     }
                }
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(err => console.error(err));
}
