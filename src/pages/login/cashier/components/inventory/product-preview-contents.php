<link rel="stylesheet" href="MainContent.css">
<link rel="stylesheet" href="product-preview.css">

<div id="main-content">
    <div class="tooltip" id="tooltip" style="display: none;">Tooltip Text</div>

    <div class="container">
        <div class="header">
            <h1>Product Details</h1>
            <div class="btn-wrapper">
                <a href="items.php" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
            </div>
        </div>

        <div class="btncontents">
            <a href="#" class="active" data-category="pizza"><img src="../../assets/pizza.png" class="img-btn-link">Pizza</a>
            <a href="#" data-category="pasta"><img src="../../assets/spag.png" class="img-btn-link">Pasta</a>
            <a href="#" data-category="beverages"><img src="../../assets/bev.png" class="img-btn-link">Beverages</a>
        </div>
        <br>
        <div class="product_container" id="product-container">
        </div>

        <script>
            const categoryLinks = document.querySelectorAll('.btncontents a');
            const productContainer = document.getElementById('product-container');

            // Function to load products dynamically
            function loadProducts(category) {
                productContainer.innerHTML = '<p id="loading-text">Loading products, please wait...</p>';

                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'load-products.php?category=' + category, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        setTimeout(() => {
                            productContainer.innerHTML = xhr.responseText; // Delay rendering to reduce flicker
                        }, 500); // Smooth delay
                    } else {
                        productContainer.innerHTML = '<p>Error loading products. Please try again later.</p>';
                    }
                };
                xhr.send();
            }

            // Check if "category" exists in the URL
            const urlParams = new URLSearchParams(window.location.search);
            let category = urlParams.get('category');

            if (!category) {
                // Default to "pizza"
                category = 'pizza';

                // Update the URL with the default category
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('category', category);
                window.history.replaceState({}, '', newUrl);

                // Load products for the default category
                loadProducts(category);

                setTimeout(() => {
                    productContainer.style.opacity = 0; // Fade out before reload
                    setTimeout(() => {
                        window.location.reload(); // Trigger reload
                    }, 300); // Smooth transition before reload
                }, 100);
            } else {
                // Load products for the category in the URL
                loadProducts(category);
            }

            // Set the active class based on the category in the URL
            categoryLinks.forEach(link => {
                const linkCategory = link.getAttribute('data-category');
                if (linkCategory === category) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            // Event listeners for category links
            categoryLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    categoryLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');

                    const category = link.getAttribute('data-category');
                    loadProducts(category);

                    // Update URL without reloading
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.set('category', category);
                    window.history.pushState({}, '', newUrl);

                    // Reload the page after loading products
                    setTimeout(() => {
                        window.location.reload(); // Trigger reload
                    }, 200); // Delay reload after loading products
                });
            });
        </script>

    </div>
</div>