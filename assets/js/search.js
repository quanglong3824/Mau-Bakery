document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const resultsContainer = document.getElementById('searchResults');
    let dobounceTimer;

    if (!searchInput || !resultsContainer) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(dobounceTimer);

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.classList.remove('active');
            return;
        }

        // Debounce to avoid too many requests
        dobounceTimer = setTimeout(() => {
            fetchResults(query);
        }, 300);
    });

    function fetchResults(query) {
        fetch(`api/search.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayResults(data);
            })
            .catch(err => {
                console.error('Search error:', err);
            });
    }

    function displayResults(products) {
        resultsContainer.innerHTML = ''; // Clear

        if (products.length === 0) {
            resultsContainer.innerHTML = '<div class="no-results">Không tìm thấy sản phẩm nào</div>';
            resultsContainer.classList.add('active');
            return;
        }

        products.forEach(product => {
            const item = document.createElement('a');
            item.href = `index.php?page=product_detail&id=${product.id}`;
            item.className = 'search-item';
            
            // Handle optional image or placeholder
            const imgSrc = product.image ? product.image : 'assets/images/no-image.png';

            item.innerHTML = `
                <img src="${imgSrc}" alt="${product.name}">
                <div class="search-info">
                    <span class="search-name">${product.name}</span>
                    <span class="search-price">${product.price_formatted}</span>
                </div>
            `;
            resultsContainer.appendChild(item);
        });

        resultsContainer.classList.add('active');
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.remove('active');
        }
    });

    // Show again if focused and has content
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && resultsContainer.children.length > 0) {
            resultsContainer.classList.add('active');
        }
    });
});
