function updateCart(index, newQuantity) {
    if (newQuantity < 1) {
        if (!confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) return;
        removeFromCart(index);
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update_cart');
    formData.append('index', index);
    formData.append('quantity', newQuantity);

    fetch('index.php?page=cart_action', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload(); // Reload to update totals simply
            }
        });
}

function removeFromCart(index) {
    if (!confirm('Xác nhận xóa sản phẩm này?')) return;

    const formData = new FormData();
    formData.append('action', 'remove_from_cart');
    formData.append('index', index);

    fetch('index.php?page=cart_action', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            }
        });
}
