<?php
// Get the current date
$currentDate = new DateTime();

// Get the first day of the current month
$firstDayOfMonth = new DateTime("first day of this month");

// Find the first Monday of the month
$firstMonday = clone $firstDayOfMonth;
$firstMonday->modify('next monday');

// Function to calculate the start and end date for each week, starting from the first Monday
function getWeekRange($weekNumber, $firstMonday)
{
    // Get the starting date of the first week (first Monday)
    $firstWeekStartDate = clone $firstMonday;

    // Calculate the start date of the given week
    $startDate = clone $firstWeekStartDate;
    $startDate->modify('+' . ($weekNumber - 1) . ' week'); // Move to the correct week

    // Calculate the end date (6 days after the start date)
    $endDate = clone $startDate;
    $endDate->modify('+6 days');

    // Format the dates to display in a readable format (e.g., "November 4 - November 10")
    return [
        'start' => $startDate->format('F j, Y'),  // e.g. "November 4, 2024"
        'end' => $endDate->format('F j, Y')       // e.g. "November 10, 2024"
    ];
}

// Get the current week number by calculating the difference between the current date and the first Monday of the month
$weekNumber = ceil(($currentDate->format('d') + $firstMonday->format('N') - 1) / 7);

// Check if the 'week' parameter is in the URL
if (!isset($_GET['week'])) {
    // If 'week' is not in the URL, set the current week as the default week
    header("Location: ?week=$weekNumber");
    exit; // Make sure to call exit after header to prevent further code execution
}

// Retrieve the 'week' parameter from the URL
$currentWeekFromUrl = isset($_GET['week']) ? (int)$_GET['week'] : $weekNumber;

// Get the range for the selected week
$weekRange = getWeekRange($currentWeekFromUrl, $firstMonday);
?>

<link rel="stylesheet" href="week-overview.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Weekly Inventory Overview</h1>
            <div class="btn-wrapper">
                <a href="items.php" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
            </div>
        </div>

        <div class="btn-wrapper2">
            <select id="view-mode" onchange="changeViewMode()">
                <option value="weekly" selected>Weekly</option>
                <option value="monthly">Monthly</option>
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

            <!-- Week Selector Form -->
            <form action="" method="GET">
                <select id="view-mode" name="week" onchange="this.form.submit()">
                    <?php
                    // Loop through weeks to generate options
                    for ($i = 1; $i <= 4; $i++) {
                        $weekRangeOption = getWeekRange($i, $firstMonday);
                        $selected = ($i == $currentWeekFromUrl) ? 'selected' : ''; // Mark current week as selected
                        echo "<option value='$i' $selected>Week $i | {$weekRangeOption['start']} - {$weekRangeOption['end']}</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <!-- Table Container Section -->
        <div class="table_container">
            <!-- Display Week Range in the Header with Year -->
            <h2>Week <?php echo $currentWeekFromUrl; ?> | <?php echo $weekRange['start']; ?> - <?php echo $weekRange['end']; ?></h2>

            <div class="btns_container">
                <a href="export-pdf-weekly.php?week=<?php echo $currentWeekFromUrl; ?>" class="icon_btn"><img src="../../assets/save.svg" alt="Save"></a>
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
            <div class="table" id="week-table">
                <?php include 'week-overviewtable.php'; ?>
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
<script src="week-overview.js"></script>