// Simple FAQ Search
document.getElementById('faq-search').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const cats = document.querySelectorAll('.faq-category');
    
    cats.forEach(cat => {
        let hasVisible = false;
        const items = cat.querySelectorAll('.faq-item');
        
        items.forEach(item => {
            const q = item.querySelector('.faq-question').innerText.toLowerCase();
            if(q.includes(term)) {
                item.style.display = 'block';
                hasVisible = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        cat.style.display = hasVisible ? 'block' : 'none';
    });
});
