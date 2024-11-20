<link rel="stylesheet" href="archive.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Archive of Inventory Records</h1>
            <div class="btn-wrapper">
                <a href="items.php" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
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
                <a href="#" id="export-btn" class="icon_btn">
                    <img src="../../assets/save.svg" alt="Save">
                </a>

                <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                <!-- Sorting Options -->
                <div class="sort-container">
                    <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                    <span class="sort-label">SORT BY:</span>
                    <select class="select" id="sort" onchange="updateSort()">
                        <option value="name" selected>Name</option>
                        <option value="itemID">Code</option>
                        <option value="uom">Unit of Measurement</option>
                        <option value="submitted_by">Submitted By</option>
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
            <div class="table" id="archive-table">
                <?php include 'archive-table.php'; ?>
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
<script src="archive.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const exportBtn = document.getElementById('export-btn');
        const dateInput = document.getElementById('inventory-date');

        if (exportBtn && dateInput) {
            // Update export button href when the date input changes
            dateInput.addEventListener('change', function() {
                const selectedDate = this.value; // Get the selected date
                if (selectedDate) {
                    exportBtn.href = `export-archive.php?date=${encodeURIComponent(selectedDate)}`;
                } else {
                    exportBtn.href = "#"; // Default behavior if no date is selected
                }
            });

            // Set the export button's href on page load if a date is pre-selected
            const currentUrl = new URL(window.location.href);
            const initialDate = currentUrl.searchParams.get('date');
            if (initialDate) {
                dateInput.value = initialDate; // Set the date input value
                exportBtn.href = `export-archive.php?date=${encodeURIComponent(initialDate)}`;
            }
        }
    });
</script>