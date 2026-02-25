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

    // 2. Coupon DB check
    const btnApply = document.querySelector('.btn-apply');
    const couponInput = document.querySelector('.coupon-group input');
    
    let currentDiscount = 0;
    
    if(btnApply && couponInput) {
        btnApply.addEventListener('click', function(e) {
            e.preventDefault();
            const code = couponInput.value.trim();
            const subtotal = parseInt(subtotalEl.getAttribute('data-amount')) || 0;
            
            if (!code) {
                // If input is empty, clear the discount
                currentDiscount = 0;
                if (document.getElementById('checkout-discount-row')) document.getElementById('checkout-discount-row').style.display = 'none';
                if (document.getElementById('hidden_coupon_code')) document.getElementById('hidden_coupon_code').value = '';
                if (document.getElementById('hidden_discount_amount')) document.getElementById('hidden_discount_amount').value = 0;
                recalculateTotal();
                return;
            }
            
            fetch('api/apply_voucher.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ code: code, subtotal: subtotal })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    currentDiscount = data.discount_amount;
                    
                    // Update UI 
                    let discountRow = document.getElementById('checkout-discount-row');
                    if(currentDiscount > 0) {
                        if(discountRow) {
                            discountRow.style.display = 'flex';
                            document.getElementById('checkout-discount').innerText = '-' + new Intl.NumberFormat('vi-VN').format(currentDiscount) + 'đ';
                        }
                    }
                    
                    // Set hidden inputs
                    if (document.getElementById('hidden_coupon_code')) document.getElementById('hidden_coupon_code').value = data.coupon_code;
                    if (document.getElementById('hidden_discount_amount')) document.getElementById('hidden_discount_amount').value = currentDiscount;
                    
                    recalculateTotal();
                } else {
                    alert(data.message);
                    currentDiscount = 0;
                    if (document.getElementById('checkout-discount-row')) document.getElementById('checkout-discount-row').style.display = 'none';
                    if (document.getElementById('hidden_coupon_code')) document.getElementById('hidden_coupon_code').value = '';
                    if (document.getElementById('hidden_discount_amount')) document.getElementById('hidden_discount_amount').value = 0;
                    recalculateTotal();
                }
            })
            .catch(err => {
                console.error(err);
                alert("Đã xảy ra lỗi kết nối.");
            });
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
        const selectedOption = districtSelect.options[districtSelect.selectedIndex]? districtSelect.options[districtSelect.selectedIndex] : null;
        let shippingFee = 0;
        if(selectedOption) shippingFee = parseInt(selectedOption.getAttribute('data-fee')) || 0;
        
        // Get Subtotal
        const subtotal = parseInt(subtotalEl.getAttribute('data-amount')) || 0;
        
        // Calculate Total
        let total = subtotal + shippingFee - currentDiscount;
        if (total < 0) total = 0;
        
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
    
    // Auto-cancel discount if user clears the input
    if (couponInput) {
        couponInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                currentDiscount = 0;
                if (document.getElementById('checkout-discount-row')) document.getElementById('checkout-discount-row').style.display = 'none';
                if (document.getElementById('hidden_coupon_code')) document.getElementById('hidden_coupon_code').value = '';
                if (document.getElementById('hidden_discount_amount')) document.getElementById('hidden_discount_amount').value = 0;
                recalculateTotal();
            }
        });
    }
    
    // Initial Calc (To ensure numbers are formatted)
    recalculateTotal();

});
