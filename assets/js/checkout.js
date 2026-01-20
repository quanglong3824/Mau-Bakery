document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Handle Payment Method Selection Styling
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    
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

    // Initialize Payment Radios
    updateActiveRadio(document.querySelectorAll('.radio-input'));

    // Listen for Payment Method Change
    document.body.addEventListener('change', function(e) {
        if(e.target.classList.contains('radio-input') && e.target.name === 'payment_method') {
             updateActiveRadio(document.querySelectorAll('input[name="payment_method"]'));
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

    // 3. Update Total Logic (Based on District)
    const districtSelect = document.getElementById('district-select');
    const subtotalEl = document.getElementById('checkout-subtotal');
    const shippingEl = document.getElementById('checkout-shipping');
    const totalEl = document.getElementById('checkout-total');
    const hiddenShippingEl = document.getElementById('hidden_shipping_fee');
    
    function recalculateTotal() {
        // Get fee from selected option
        const selectedOption = districtSelect.options[districtSelect.selectedIndex];
        const shippingFee = parseInt(selectedOption.getAttribute('data-fee')) || 0;
        
        // Get Subtotal
        const subtotal = parseInt(subtotalEl.getAttribute('data-amount'));
        
        // Calculate Total
        const total = subtotal + shippingFee;
        
        // Update DOM
        shippingEl.innerText = new Intl.NumberFormat('vi-VN').format(shippingFee) + 'đ';
        totalEl.innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        
        // Update hidden input if exists
        if(hiddenShippingEl) hiddenShippingEl.value = shippingFee;
        
        // Update total data attr
        totalEl.setAttribute('data-amount', total);
    }

    // Listen for District Change
    if(districtSelect) {
        districtSelect.addEventListener('change', recalculateTotal);
    }
    
    // Initial Calc (To ensure numbers are formatted)
    recalculateTotal();

});
