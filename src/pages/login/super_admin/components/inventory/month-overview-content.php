<?php
include '../../connection/database.php';
// Get the current date
$currentDate = new DateTime();

// Get the current month and year
$currentMonthNumber = $currentDate->format('n');
$currentYear = $currentDate->format('Y');


// Check if the 'month' or 'year' parameters are in the URL
if (!isset($_GET['month']) || !isset($_GET['year'])) {
    // If either 'month' or 'year' is not in the URL, set the current month and year as the default
    header("Location: ?month=$currentMonthNumber&year=$currentYear");
    exit; // Make sure to call exit after header to prevent further code execution
}

// Retrieve the 'month' and 'year' parameters from the URL
$selectedMonthNumber = isset($_GET['month']) ? (int)$_GET['month'] : $currentMonthNumber;
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;

// List of months for the dropdown
$months = [
    1 => "January",
    2 => "February",
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "August",
    9 => "September",
    10 => "October",
    11 => "November",
    12 => "December"
];

// Query to get distinct years from the records_inventory table
$sql = "SELECT DISTINCT YEAR(inventory_date) AS year FROM records_inventory WHERE inventory_date IS NOT NULL ORDER BY year DESC";
$result = $conn->query($sql);

$availableYears = [];
if ($result->num_rows > 0) {
    // Store available years in an array
    while ($row = $result->fetch_assoc()) {
        $availableYears[] = $row['year'];
    }
} else {
    // If no data is found, fall back to the current year as default
    $availableYears[] = $currentYear;
}

// Close the database connection
$conn->close();
?>

<link rel="stylesheet" href="month-overview.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Monthly Inventory Overview</h1>
            <div class="btn-wrapper">
                <a href="items.php" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
            </div>
        </div>

        <div class="btn-wrapper2">
            <select id="view-mode" onchange="changeViewMode()">
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
            </select>

            <script>
                // Function to handle the change in view mode (week or month)
                function changeViewMode() {
                    var viewMode = document.getElementById('view-mode').value;
                    if (viewMode === 'weekly') {
                        window.location.href = 'week-overview.php';
                    } else if (viewMode === 'monthly') {
                        window.location.href = 'month-overview.php';
                    }
                }
            </script>

            <!-- Month and Year Selector Form -->
            <form action="" method="GET">
                <select id="view-mode" name="month" onchange="this.form.submit()">
                    <?php
                    // Loop through months to generate options
                    foreach ($months as $monthNumber => $monthName) {
                        $selected = ($monthNumber == $selectedMonthNumber) ? 'selected' : ''; // Mark current month as selected
                        echo "<option value='$monthNumber' $selected>$monthName</option>";
                    }
                    ?>
                </select>

                <select id="view-mode" name="year" onchange="this.form.submit()">
                    <?php
                    // Loop through available years to generate options
                    foreach ($availableYears as $year) {
                        $selected = ($year == $selectedYear) ? 'selected' : ''; // Mark selected year
                        echo "<option value='$year' $selected>$year</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <!-- Table Container Section -->
        <div class="table_container">
            <!-- Display Selected Month and Year in the Header -->
            <h2>Month of <?php echo $months[$selectedMonthNumber]; ?>, <?php echo $selectedYear; ?></h2>

            <div class="btns_container">
                <a href="export-pdf-monthly.php?month=<?php echo $selectedMonthNumber; ?>&year=<?php echo $selectedYear; ?>" class="icon_btn"><img src="../../assets/save.svg" alt="Save"></a>
                <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                <!-- Sorting Options -->
                <div class="sort-container">
                    <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                    <span class="sort-label">SORT BY:</span>
                    <select class="select" id="sort" onchange="updateSort()">
                        <option value="name" selected>Name</option>
                        <option value="itemID">Code</option>
                        <option value="uom">Unit of Measurement</option>
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
            <div class="table" id="month-table">
                <?php include 'month-overviewtable.php'; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Inclusions -->
<?php include 'add-items.php'; ?>
<?php include 'submit-report.php'; ?>
<?php include 'SuccessErrorModal.php'; ?>
<script src="SuccessErrorModal.js"></script>
</div>
<script src="month-overview.js"></script>