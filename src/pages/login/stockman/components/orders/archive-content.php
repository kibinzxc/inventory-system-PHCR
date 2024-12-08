<link rel="stylesheet" href="archive.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Order Logs</h1>
            <div class="btn-wrapper">
                <a href="order-count.php" class="btn"><img src="../../assets/external-link.svg" alt=""> Check Order Count </a>
                <a href="orders.php" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
            </div>
        </div>

        <div class="table_container2">
            <div class="sort-container2">
                <form action="" method="GET">
                    <label class="sort-label2" for="inventory-date">SELECT DATE:</label>
                    <input class="select3" type="date" name="inventory_date" id="inventory-date">
                </form>
            </div>
        </div>

        <!-- Table Container Section -->
        <div class="table_container">
            <h2 id="selected-date"></h2>

            <!-- Utility Buttons and Sorting Options -->
            <div class="btns_container">

                <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                <!-- Sorting Options -->
                <div class="sort-container">
                    <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                    <span class="sort-label">SORT BY:</span>
                    <select class="select" id="sort" onchange="updateSort()">
                        <option value="invID" selected>Invoice ID</option>
                        <option value="order_type">Order Type</option>
                        <option value="transaction_date">Date</option>
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

        </div>

        <div class="loader" id="loader" style="display:none;"></div>
        <div class="table" id="archive-table">
            <?php include 'archive-table.php'; ?>
        </div>

        <!-- Mobile View Access Note -->
        <blockquote class="mobile-note">
            <strong>Note:</strong> On mobile devices, access is limited to viewing only. You cannot edit, add, or remove content.
        </blockquote>
    </div>

</div>
<script src="archive.js"></script>