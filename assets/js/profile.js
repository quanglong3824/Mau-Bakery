document.addEventListener('DOMContentLoaded', function() {
    
    const navItems = document.querySelectorAll('.nav-item[data-target]');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    // Function to switch tab
    function switchTab(targetId) {
        // Update Nav
        navItems.forEach(item => {
            if(item.getAttribute('data-target') === targetId) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
        
        // Update Content
        tabPanes.forEach(pane => {
            if(pane.id === targetId) {
                pane.classList.add('active');
            } else {
                pane.classList.remove('active');
            }
        });
    }

    // Click Event
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-target');
            switchTab(target);
        });
    });

    // Handle "Save" buttons (Mock)
    const saveBtns = document.querySelectorAll('.btn-save');
    saveBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Đã lưu';
            this.style.background = '#4CAF50';
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.background = '';
            }, 2000);
        });
    });

});
