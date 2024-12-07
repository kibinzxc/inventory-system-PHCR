<link rel="stylesheet" href="MainContent.css">

<div id="main-content">
    <!-- Tooltip for button hints -->
    <div class="tooltip" id="tooltip" style="display: none;">Tooltip Text</div>

    <!-- Header Section -->
    <div class="container">
        <div class="header">
            <h1>Product List</h1>
            <div class="btn-wrapper">
                <a href="week-overview.php" target="_blank" class="btn"><img src="../../assets/external-link.svg" alt=""> Inventory Overview</a>
                <a href="product-preview.php" class="btn"><img src="../../assets/instagram.svg" alt=""> Product Details</a>
                <a href="archive.php" class="btn" onclick="openArchiveModal()"><img src="../../assets/file-text.svg" alt=""> Archive</a>
            </div>
        </div>

        <!-- Action Buttons Section -->
        <br>
        <div class="btn-wrapper2">
            <a href="add-product.php" class="btn2" onclick="window.open('add-product.php', '_blank', 'width=550px,height=700px'); return false;">
                <img src="../../assets/plus-circle.svg" alt=""> Add New Product
            </a>
        </div>

        <div class="table_container">
            <?php include 'inventoryCards.php'; ?>
        </div>

        <div class="btncontents">
            <!-- <a href="https://www.flaticon.com/free-icons/inventory" title="inventory icons">Inventory icons created by Nhor Phai - Flaticon</a> -->
            <a href="items.php"><img src="../../assets/inventory.png" class="img-btn-link">Daily Inventory</a>
            <!-- <a href="https://www.flaticon.com/free-icons/summary" title="summary icons">Summary icons created by Flat Icons - Flaticon</a> -->
            <a href="daily-summary.php"><img src="../../assets/text-file.png" class="img-btn-link">Daily Summary</a>
            <!-- <a href="https://www.flaticon.com/free-icons/restaurant" title="restaurant icons">Restaurant icons created by Freepik - Flaticon</a> -->
            <a href="ingredients.php"><img src="../../assets/packaging.png" class="img-btn-link">Items</a>
            <a href="products.php" class="active"><img src="../../assets/cutlery.png" class="img-btn-link">Product List</a>
        </div>

        <br>
        <!-- Table Container Section -->
        <div class="table_container">
            <!-- Utility Buttons and Sorting Options -->
            <div class="btns_container">
                <a href="#" class="icon_btn"><img src="../../assets/save.svg" alt="Save"></a>
                <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                <!-- Sorting Options -->
                <div class="sort-container">
                    <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                    <span class="sort-label">SORT BY:</span>
                    <select class="select" id="sort" onchange="updateSort()">
                        <option value="name" selected>Name</option>
                    </select>

                    <span class="sort-label">ORDER:</span>
                    <select class="select2" id="sortOrder" onchange="updateSortOrder()">
                        <option value="asc" selected>Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>

                <!-- Refresh Button with Circular Loader -->
                <a href="#" class="icon_btn" id="refresh-btn">
                    <div class="refresh-container">
                        <img src="../../assets/refresh-ccw.svg" alt="Refresh">
                        <span class="loader-circle"></span> <!-- Circular Loader -->
                    </div>
                </a>
            </div>

            <!-- Table Loader and Content -->
            <div class="loader" id="loader" style="display:none;"></div>
            <div class="table" id="product-table">
                <?php include 'productTable.php'; ?>
            </div>
        </div>

        <!-- Mobile View Access Note -->
        <blockquote class="mobile-note">
            <strong>Note:</strong> On mobile devices, access is limited to viewing only. You cannot edit, add, or remove content.
        </blockquote>
    </div>

    <!-- Modal Inclusions -->
    <?php include 'add-items.php'; ?>
    <?php include 'submit-report.php'; ?>
    <?php include 'SuccessErrorModal.php'; ?>
    <script src="SuccessErrorModal.js"></script>
</div>
<script src="productTable.js"></script>