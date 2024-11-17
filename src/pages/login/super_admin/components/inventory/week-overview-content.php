<?php
// Set the timezone if necessary
date_default_timezone_set('Asia/Manila'); // Adjust according to your timezone

// Get the current date
$currentDate = new DateTime();

// Calculate the current week's start (Monday) and end (Sunday)
$startOfWeek = clone $currentDate;
$startOfWeek->modify('monday this week'); // Modify to get the Monday of the current week
$endOfWeek = clone $currentDate;
$endOfWeek->modify('sunday this week'); // Modify to get the Sunday of the current week

// Format the dates to display in the desired format
$startDateFormatted = $startOfWeek->format('M j, Y');
$endDateFormatted = $endOfWeek->format('M j, Y');
?>

<link rel="stylesheet" href="week-overview.css">

<div id="main-content">
    <!-- Tooltip for button hints -->
    <div class="tooltip" id="tooltip" style="display: none;">Tooltip Text</div>

    <!-- Header Section -->
    <div class="container">
        <div class="header">
            <h1>Current Week Overview</h1>
            <div class="btn-wrapper">
                <a href="javascript:history.back()" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
            </div>
        </div>

        <!-- Dynamic Date Range Display -->
        <h2><?php echo $startDateFormatted . ' - ' . $endDateFormatted; ?></h2>

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