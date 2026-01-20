/**
 * Auth Pages Logic
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check for success message on Register page to trigger redirect
    const successMsg = document.querySelector('.success-msg');
    
    if (successMsg && successMsg.textContent.includes('Đăng ký thành công')) {
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 2000);
    }
});
