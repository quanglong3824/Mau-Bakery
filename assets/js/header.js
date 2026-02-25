window.addEventListener('scroll', function () {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Mobile Menu Logic
const mobileBtn = document.querySelector('.mobile-menu-btn');
const mobileMenu = document.querySelector('.mobile-menu-overlay');

function toggleMenu() {
    const isActive = mobileMenu.classList.toggle('active');
    mobileBtn.classList.toggle('active');

    // Optional: Toggle body scroll
    if (isActive) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Toggle button click
if(mobileBtn) {
    mobileBtn.addEventListener('click', function (e) {
        e.stopPropagation(); // Prevent document click from firing immediately
        toggleMenu();
    });
}

// Close when clicking overlay (backdrop)
if(mobileMenu) {
    mobileMenu.addEventListener('click', function (event) {
        if (event.target === mobileMenu) {
            toggleMenu();
        }
    });
}

// Also close when pressing Escape key
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('active')) {
        toggleMenu();
    }
});
