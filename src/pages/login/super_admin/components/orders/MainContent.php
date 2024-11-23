<link rel="stylesheet" href="MainContent.css" />
<div id="main-content">
    <!-- Header Section -->
    <div class="container">
        <div class="header">
            <h1>Orders</h1>
            <div class="btn-wrapper">
                <a href="week-overview.php" target="_blank" class="btn"><img src="../../assets/external-link.svg" alt=""> Online Orders</a>
                <a href="archive.php" class="btn" onclick="openArchiveModal()"><img src="../../assets/file-text.svg" alt=""> Logs</a>
            </div>
        </div>
        <br>
        <!-- Flex container for table and panel -->
        <div class="content-wrapper">
            <div class="table-product-container">
                <div class="table_container">
                    <!-- Utility Buttons and Sorting Options -->
                    <div class="btns_container">
                        <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                        <!-- Sorting Options -->
                        <div class="sort-container">
                            <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                            <span class="sort-label">Category:</span>
                            <select class="select" id="sort" onchange="updateSort()">
                                <option value="pizza" selected>Pizza</option>
                                <option value="pasta">Pasta</option>
                                <option value="beverages">Beverages</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="product_container" id="product-container">
                </div>
            </div>

            <div class="panel-container">
                test
            </div>
        </div>
        <script>
            const categoryLinks = document.querySelectorAll('.btncontents a');
            const productContainer = document.getElementById('product-container');

            // Function to load products dynamically based on category
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

            // Function to update the URL without reloading the page
            function updateUrl(category) {
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('category', category);
                window.history.pushState({}, '', newUrl);
            }

            // Check if "category" exists in the URL
            const urlParams = new URLSearchParams(window.location.search);
            let category = urlParams.get('category');

            if (!category) {
                // Default to "pizza"
                category = 'pizza';

                // Update the URL with the default category
                updateUrl(category);

                // Load products for the default category
                loadProducts(category);
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
                    updateUrl(category);
                });
            });

            // Event listener for the category select dropdown
            const categorySelect = document.getElementById('sort');
            categorySelect.addEventListener('change', function() {
                const category = categorySelect.value;
                loadProducts(category);
                updateUrl(category);
            });
        </script>
    </div>
</div>