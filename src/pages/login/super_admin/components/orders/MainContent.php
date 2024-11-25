<?php include 'cash-input.php'; ?>
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
                        <!-- Search Field -->
                        <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                        <!-- Sorting Options -->
                        <div class="sort-container">
                            <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                            <span class="sort-label">Category:</span>
                            <select class="select" id="sort">
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

            <!-- Panel Container -->
            <div class="panel-container">
                <!-- First Part: Panel Cards -->
                <div class="panel-cards">
                    <div class="panel-card">
                        <input type="number" class="quantity-input" value="1" min="1" />
                        <div class="name-sizepanel">
                            <span class="panel-name">Cheesy Lovers</span>
                            <span class="panel-size">9inch Pan Pizza</span>
                        </div>
                        <span class="panel-price"><?php echo number_format(299, 2); ?></span>
                        <button class="delete-btn" id="delete-btn">X</button>
                    </div>
                </div>
                <hr style="border: 1px solid #DBDBDB; width:100%;">
                <!-- Second Part: Panel Details (Total) -->
                <div class="panel-details">
                    <div class="left-side">
                        <span id="total-items">0</span>
                        <span id="item-label">Items</span>
                    </div>
                    <div class="right-side">
                        <div class="total-container">
                            <span class="marginspan">Vatable:</span>
                            <span id="subtotal">₱0.00</span>
                        </div>
                        <div class="total-container space">
                            <span class="marginspan">VAT 12%:</span>
                            <span id="vat">₱0.00</span>
                        </div>
                        <div class="total-container space">
                            <span id="total-price-label">Total:</span>
                            <span id="total-price">₱0.00</span>
                        </div>
                        <!-- Add Cash and Change -->
                        <div class="total-container space" id="cash-container">
                            <span class="marginspan">Cash:</span>
                            <span id="cash">₱0.00</span>
                        </div>
                        <div class="total-container space" id="change-container">
                            <span class="marginspan">Change:</span>
                            <span id="change">₱0.00</span>
                        </div>
                    </div>
                </div>


                <!-- Third Part: Clear and Submit Buttons -->
                <div class="panel-actions">
                    <button id="clear-btn" class="action-btn"><img src="../../assets/trash.svg" alt=""></button>
                    <a href="#" id="payment-btn" class="btn">
                        <span id="payment-btn-text">Payment</span>
                        <img class="arrow" src="../../assets/arrow-right.svg" alt="Arrow Right">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const productContainer = document.getElementById('product-container');
    const categorySelect = document.getElementById('sort');
    const searchBar = document.getElementById('search');

    // Function to load products dynamically
    function loadProducts(category = 'pizza', searchQuery = '') {
        productContainer.innerHTML = '<p id="loading-text">Loading products, please wait...</p>';

        const xhr = new XMLHttpRequest();
        xhr.open(
            'GET',
            `load-products.php?category=${category}&search=${encodeURIComponent(searchQuery)}`,
            true
        );
        xhr.onload = function() {
            if (xhr.status === 200) {
                setTimeout(() => {
                    productContainer.innerHTML = xhr.responseText;
                }, 500); // Smooth delay
            } else {
                productContainer.innerHTML = '<p>Error loading products. Please try again later.</p>';
            }
        };
        xhr.send();

    }


    // Function to update the URL without reloading the page
    function updateUrl(category, searchQuery = '') {
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('category', category);
        if (searchQuery) newUrl.searchParams.set('search', searchQuery);
        else newUrl.searchParams.delete('search');
        window.history.pushState({}, '', newUrl);
    }

    // Event: Search bar input
    searchBar.addEventListener('input', function() {
        const category = categorySelect.value || 'pizza';
        const searchQuery = searchBar.value.trim();
        loadProducts(category, searchQuery);
        updateUrl(category, searchQuery);
    });

    // Event: Dropdown category change
    categorySelect.addEventListener('change', function() {
        const category = categorySelect.value;
        const searchQuery = searchBar.value.trim();
        loadProducts(category, searchQuery);
        updateUrl(category, searchQuery);
    });

    // Load products on page load based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    let category = urlParams.get('category') || 'pizza'; // Default category
    let searchQuery = urlParams.get('search') || '';
    categorySelect.value = category; // Set dropdown value
    searchBar.value = searchQuery; // Set search bar value

    loadProducts(category, searchQuery);
</script>
<script src="pos.js"></script>

</div>
</div>