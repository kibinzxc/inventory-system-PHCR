<?php
date_default_timezone_set('Asia/Manila');
$currentDate = new DateTime('now', new DateTimeZone('Asia/Manila'));

// Get the first day of the current month
$firstDayOfMonth = new DateTime("first day of this month", new DateTimeZone('Asia/Manila'));

// Find the first Monday of the month
$firstMonday = clone $firstDayOfMonth;
if ($firstMonday->format('N') != 1) { // If not Monday (1 is Monday)
    $firstMonday->modify('next monday');
}

// Function to calculate the start and end date for each week, starting from the first Monday
function getWeekRange($weekNumber, $firstMonday)
{
    $firstWeekStartDate = clone $firstMonday;
    $startDate = clone $firstWeekStartDate;
    $startDate->modify('+' . ($weekNumber - 1) . ' week');
    $endDate = clone $startDate;
    $endDate->modify('+6 days');

    return [
        'start' => $startDate->format('F j, Y'),
        'end' => $endDate->format('F j, Y')
    ];
}

// Calculate the current week number
$diffFromFirstMonday = $currentDate->diff($firstMonday)->days; // Days between first Monday and today
if ($currentDate < $firstMonday) {
    $weekNumber = 1; // Before the first Monday of the month
} else {
    $weekNumber = ceil(($diffFromFirstMonday + 1) / 7);
}

// Redirect to add the current week number if not provided in the URL
if (!isset($_GET['week'])) {
    header("Location: ?week=$weekNumber");
    exit;
}

// Retrieve the week from the URL
$currentWeekFromUrl = isset($_GET['week']) ? (int)$_GET['week'] : $weekNumber;

// Get the date range for the selected week
$weekRange = getWeekRange($currentWeekFromUrl, $firstMonday);

?>



<link rel="stylesheet" href="week-overview.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Weekly Orders</h1>
            <div class="btn-wrapper">
                <a href="order-logs.php" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
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
                <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                <!-- Sorting Options -->
                <div class="sort-container">
                    <img src="../../assets/filter.svg" alt="Filter" class="filter_icon">
                    <span class="sort-label">SORT BY:</span>
                    <select class="select" id="sort" onchange="updateSort()">
                        <option value="name">Name</option>
                        <option value="order_count" selected>Order Count</option>
                    </select>

                    <span class="sort-label">ORDER:</span>
                    <select class="select2" id="sortOrder" onchange="updateSortOrder()">
                        <option value="asc">Ascending</option>
                        <option value="desc" selected>Descending</option>
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