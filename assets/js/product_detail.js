document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Image Gallery Logic
    const mainImage = document.getElementById('main-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Update Main Image
            const newSrc = this.getAttribute('src');
            
            // Fade effect
            mainImage.style.opacity = '0';
            
            setTimeout(() => {
                mainImage.src = newSrc;
                mainImage.style.opacity = '1';
            }, 200);
            
            // Update Active State
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // 2. Quantity Logic
    const qtyInput = document.getElementById('qty-input');
    const btnMinus = document.querySelector('.btn-minus');
    const btnPlus = document.querySelector('.btn-plus');
    
    btnMinus.addEventListener('click', () => {
        let val = parseInt(qtyInput.value);
        if (val > 1) {
            qtyInput.value = val - 1;
        }
    });
    
    btnPlus.addEventListener('click', () => {
        let val = parseInt(qtyInput.value);
        if (val < 20) { // Limit max quantity
            qtyInput.value = val + 1;
        }
    });

    // 3. Size Selection Logic
    const sizeBtns = document.querySelectorAll('.size-btn');
    const priceDisplay = document.getElementById('price-display');
    // Ensure we start with a selected price if sizes exist
    let currentPrice = priceDisplay ? parseInt(priceDisplay.innerText.replace(/\D/g, '')) : 0;
    
    sizeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            sizeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const newPrice = parseInt(this.getAttribute('data-price'));
            currentPrice = newPrice;
            
            priceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(newPrice) + 'đ';
        });
    });

    // 4. Tabs Logic
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-tab');
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(targetId).classList.add('active');
        });
    });

    // 5. Add to Cart Logic (AJAX)
    const addToCartBtn = document.querySelector('.btn-add-cart');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            const productId = document.getElementById('product-id').value;
            const productName = document.getElementById('product-name').value;
            const productImage = document.getElementById('product-image').value;
            const quantity = parseInt(qtyInput.value);
            
            // Get selected size
            let selectedSize = null;
            const activeSizeBtn = document.querySelector('.size-btn.active');
            
            // Calculate price based on size or default
            let price = currentPrice;
            if(activeSizeBtn) {
                selectedSize = activeSizeBtn.innerText;
                price = parseInt(activeSizeBtn.getAttribute('data-price'));
            } else if (priceDisplay) {
                 // Fallback if no size buttons but price exists
                 price = parseInt(priceDisplay.innerText.replace(/\D/g, ''));
            }

            // AJAX Request
            const formData = new FormData();
            formData.append('action', 'add_to_cart');
            formData.append('product_id', productId);
            formData.append('name', productName);
            formData.append('price', price);
            formData.append('image', productImage);
            formData.append('quantity', quantity);
            if(selectedSize) formData.append('size', selectedSize);

            // Change button state
            const originalText = addToCartBtn.innerHTML;
            addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
            addToCartBtn.disabled = true;

            fetch('index.php?page=cart_action', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    addToCartBtn.innerHTML = '<i class="fas fa-check"></i> Đã thêm vào giỏ';
                    addToCartBtn.style.background = '#4CAF50';
                    
                    // Update Cart badge if exists (optional implementation later)
                    // Update Cart badge instantly
                    const cartBadges = document.querySelectorAll('.cart-badge');
                    cartBadges.forEach(badge => {
                        badge.innerText = data.cart_count;
                        // Add a small bounce animation
                        badge.style.transform = 'scale(1.2)';
                        setTimeout(() => badge.style.transform = 'scale(1)', 200);
                    });

                    setTimeout(() => {
                        addToCartBtn.innerHTML = originalText;
                        addToCartBtn.style.background = '';
                        addToCartBtn.disabled = false;
                    }, 2000);
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                    addToCartBtn.innerHTML = originalText;
                    addToCartBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối!');
                addToCartBtn.innerHTML = originalText;
                addToCartBtn.disabled = false;
            });
        });
    }

});
