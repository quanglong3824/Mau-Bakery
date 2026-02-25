const bttBtn = document.getElementById("backToTopBtn");
window.addEventListener("scroll", function () {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        bttBtn.classList.add("show");
    } else {
        bttBtn.classList.remove("show");
    }
});
bttBtn.addEventListener("click", function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});

// Hàm Thêm Vào Giỏ Hàng (Global)
function addToCart(id, name, price) {
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', id);
    formData.append('name', name);
    formData.append('price', price);
    formData.append('quantity', 1);
    formData.append('image', '');

    // Show loading/spinner on button if possible, but simplest is transparent fetch:
    fetch('index.php?page=cart_action', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Đã thêm "' + name + '" vào giỏ hàng!');
                const cartBadges = document.querySelectorAll('.cart-badge');
                cartBadges.forEach(badge => {
                    badge.innerText = data.cart_count || (parseInt(badge.innerText) + 1);
                    // Add bounce animation
                    badge.style.transform = 'scale(1.4)';
                    badge.style.transition = 'transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    setTimeout(() => badge.style.transform = 'scale(1)', 300);
                });
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi khi thêm vào giỏ hàng!');
        });
}

// Hàm Theo dõi đơn hàng chung cho toàn trang
function checkOrderGlobal(e) {
    if (e) e.preventDefault();
    const orderCode = prompt("Vui lòng nhập mã đơn hàng của bạn để tra cứu (Ví dụ: ORD-...):");
    if (orderCode && orderCode.trim() !== "") {
        window.location.href = "index.php?page=order_detail&code=" + encodeURIComponent(orderCode.trim());
    }
}
