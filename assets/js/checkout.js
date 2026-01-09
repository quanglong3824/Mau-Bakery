document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Handle Payment Method Selection Styling
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const paymentCards = document.querySelectorAll('.payment-card'); // If we use specific class
    
    // Helper to update active class on parent
    function updateActiveRadio(radios) {
        radios.forEach(radio => {
            const card = radio.closest('.radio-card');
            if(card) {
                if (radio.checked) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            }
        });
    }

    // Initialize
    updateActiveRadio(document.querySelectorAll('.radio-input'));

    // Listen
    document.body.addEventListener('change', function(e) {
        if(e.target.classList.contains('radio-input')) {
             updateActiveRadio(document.querySelectorAll('input[name="' + e.target.name + '"]'));
             
             // If Shipping changed, update total
             if(e.target.name === 'shipping_method') {
                 updateTotal(e.target);
             }
        }
    });

    // 2. Coupon Mock
    const btnApply = document.querySelector('.btn-apply');
    if(btnApply) {
        btnApply.addEventListener('click', function(e) {
            e.preventDefault();
            alert("Mã giảm giá không hợp lệ hoặc đã hết hạn!");
        });
    }

    // 3. Update Total Logic
    const subtotalEl = document.getElementById('checkout-subtotal');
    const shippingEl = document.getElementById('checkout-shipping');
    const totalEl = document.getElementById('checkout-total');
    
    function updateTotal(shippingInput) {
        const shippingCost = parseInt(shippingInput.getAttribute('data-price'));
        const subtotal = parseInt(subtotalEl.getAttribute('data-amount')); // We need to set this in PHP
        
        const total = subtotal + shippingCost;
        
        // Update DOM
        shippingEl.innerText = new Intl.NumberFormat('vi-VN').format(shippingCost) + 'đ';
        totalEl.innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        
        // Update total data attr
        totalEl.setAttribute('data-amount', total);
    }

});
