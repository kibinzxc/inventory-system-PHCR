<link rel="stylesheet" href="MainContent.css">

<div id="main-content">
    <!-- Tooltip for button hints -->
    <div class="tooltip" id="tooltip" style="display: none;">Tooltip Text</div>

    <!-- Header Section -->
    <div class="container">
        <div class="header">
            <h1>Daily Inventory</h1>
            <div class="btn-wrapper">
                <a href="#" class="btn" onclick="openCurrentWeekOverview()"> Current Week Overview</a>
                <a href="#" class="btn" onclick="openArchiveModal()"><img src="../../assets/file-text.svg" alt=""> Archive</a>
            </div>
        </div>

        <!-- Action Buttons Section -->
        <br>
        <div class="btn-wrapper2">
            <a href="#" class="btn2" onclick="openAddModal()"><img src="../../assets/plus-circle.svg" alt=""> Add New Item</a>
            <a href="#" class="btn2" onclick="openAddReport()"><img src="../../assets/edit-3.svg" alt=""> Submit Report</a>
            <a href="#" class="btn2" onclick="openEndOfDayModal()"><img src="../../assets/check.svg" alt=""> Submit End-of-Day Inventory</a>
        </div>

        <div class="table_container">
            <?php include 'inventoryCards.php'; ?>
        </div>

        <div class="btncontents">
            <!-- <a href="https://www.flaticon.com/free-icons/inventory" title="inventory icons">Inventory icons created by Nhor Phai - Flaticon</a> -->
            <a href="#" class="active"><img src="../../assets/inventory.png" class="img-btn-link">Daily Inventory</a>
            <!-- <a href="https://www.flaticon.com/free-icons/summary" title="summary icons">Summary icons created by Flat Icons - Flaticon</a> -->
            <a href="daily-summary.php"><img src="../../assets/text-file.png" class="img-btn-link">Daily Summary</a>
            <!-- <a href="https://www.flaticon.com/free-icons/restaurant" title="restaurant icons">Restaurant icons created by Freepik - Flaticon</a> -->
            <a href="products.php"><img src="../../assets/cutlery.png" class="img-btn-link">Product List</a>
        </div>

        <br>
        <!-- Table Container Section -->
        <div class="table_container">
            <!-- Utility Buttons and Sorting Options -->
            <div class="btns_container">
                <a href="export-pdf.php" class="icon_btn"><img src="../../assets/save.svg" alt="Save"></a>
                <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                <!-- Sorting Options -->
                <div class="sort-container">
                    <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                    <span class="sort-label">SORT BY:</span>
                    <select class="select" id="sort" onchange="updateSort()">
                        <option value="name" selected>Name</option>
                        <option value="itemID">Code</option>
                        <option value="uom">Unit of Measurement</option>
                        <option value="beginning">Beginning Inventory</option>
                        <option value="deliveries">Deliveries</option>
                        <option value="transfers_in">Transfers In</option>
                        <option value="transfers_out">Transfers Out</option>
                        <option value="spoilage">Spoilage</option>
                        <option value="ending">Ending Inventory</option>
                        <option value="usage">Usage</option>
                        <option value="status">Status</option>
                        <option value="last_update">Last Update</option>
                        <option value="updated_by">Updated By</option>
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
            <div class="table" id="account-table">
                <?php include 'itemsTable.php'; ?>
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
<script src="items.js"></script>