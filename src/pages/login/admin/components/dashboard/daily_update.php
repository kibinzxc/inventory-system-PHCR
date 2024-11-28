<?php
include '../../connection/database.php';
error_reporting(1);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get current time
$currentDateTime = new DateTime();
$currentTime = $currentDateTime->format('H:i'); // Current time (HH:MM)
$currentDate = $currentDateTime->format('Y-m-d'); // Current date (e.g., 2024-11-19)

// Display current date and time
// echo "Current Date and Time: " . $currentDate . " " . $currentTime . "<br>";

// Define the start and end times for "yesterday" and "today"
$startOfYesterday = new DateTime('yesterday 06:00 AM'); // Start of yesterday
$endOfYesterday = new DateTime('today 05:00 AM');  // End of yesterday (5:00 AM today)

$startOfToday = new DateTime('today 06:00 AM');  // Start of today (6:00 AM)
$endOfToday = new DateTime('tomorrow 06:00 AM'); // End of today (until 6:00 AM tomorrow)

// Display the date range for yesterday and today
// echo "Start of Yesterday: " . $startOfYesterday->format('Y-m-d H:i:s') . "<br>";
// echo "End of Yesterday: " . $endOfYesterday->format('Y-m-d H:i:s') . "<br>";
// echo "Start of Today: " . $startOfToday->format('Y-m-d H:i:s') . "<br>";
// echo "End of Today (until 6:00 AM tomorrow): " . $endOfToday->format('Y-m-d H:i:s') . "<br>";

// Step 1: Get the most recent last_update from daily_inventory
$lastUpdateQuery = "
    SELECT last_update
    FROM daily_inventory
    ORDER BY last_update DESC
    LIMIT 1;
";

// Execute the query to get the most recent last_update
$lastUpdateResult = $conn->query($lastUpdateQuery);
$lastUpdateRow = $lastUpdateResult->fetch_assoc();
$lastUpdate = new DateTime($lastUpdateRow['last_update']);
$lastUpdateDate = $lastUpdate->format('Y-m-d'); // Extract the date part
$lastUpdateTime = $lastUpdate->format('H:i'); // Extract time part

// Display last update date and time
// echo "Last Update: " . $lastUpdateDate . " " . $lastUpdateTime . "<br>";

// Step 2: Determine if the last update is considered from yesterday or today
if ($lastUpdate >= $startOfYesterday && $lastUpdate < $endOfYesterday) {
    // If last update is between 6:00 AM yesterday and 5:00 AM today, it's considered yesterday
    $inventoryDate = $startOfYesterday->format('Y-m-d'); // Check for yesterday's inventory
    // echo "Last update was from yesterday, checking records for: " . $inventoryDate . "<br>";
} elseif ($lastUpdate >= $startOfToday && $lastUpdate < $endOfToday) {
    // If last update is between 6:00 AM today and 5:59 AM tomorrow, it's considered today
    $inventoryDate = $currentDate; // Check for today's inventory
    // echo "Last update was from today, checking records for: " . $inventoryDate . "<br>";
} else {
    // echo "Last update is outside the expected range.<br>";
    $inventoryDate = $currentDate; // Default to today if no valid match
}

// Step 3: Check if there are records for the calculated date in `records_inventory`
$checkInventoryQuery = "
    SELECT COUNT(*) AS recordCount
    FROM records_inventory
    WHERE inventory_date = '$inventoryDate';
";

// Execute the query to check for records in `records_inventory`
$inventoryResult = $conn->query($checkInventoryQuery);
$inventoryRow = $inventoryResult->fetch_assoc();
$inventoryCount = $inventoryRow['recordCount'];

// Display inventory count and the day of the inventory record
$inventoryDay = (new DateTime($inventoryDate))->format('l'); // Get the day of the week for the inventory date
// echo "Inventory Count for $inventoryDate ($inventoryDay): " . $inventoryCount . "<br>";

// Step 4: Check if it's past 6:00 AM today
$past6AM = new DateTime(); // Current time
$isPast6AM = $past6AM > $startOfToday; // Check if it's past 6:00 AM today

// Criteria Check Messages
$criteriaMessages = [];

// Check if it's past 6:00 AM today
if (!$isPast6AM) {
    $criteriaMessages[] = "It is not yet past 6:00 AM today.";
}

// Check if there are no records for the selected date in `records_inventory`
if ($inventoryCount <= 0) {
    $criteriaMessages[] = "There are no records for $inventoryDate in records_inventory.";
}

// Step 5: Flush only if the last update is **yesterday** and conditions are met
if ($lastUpdate >= $startOfYesterday && $lastUpdate < $endOfYesterday) {
    if ($isPast6AM && $inventoryCount > 0) {
        // Step 6: Logic to flush data only if all conditions are met
        $flushQuery = "
            UPDATE daily_inventory
            SET 
                beginning = ending,   
                ending = beginning,
                deliveries = 0,
                transfers_in = 0,
                transfers_out = 0,
                spoilage = 0,
                usage_count = 0,
                status = CASE
                    WHEN beginning = 0 THEN 'out of stock'  
                    WHEN beginning BETWEEN 1 AND 5 THEN 'low stock'  
                    WHEN beginning > 5 THEN 'in stock'        
                    ELSE status
                END,
                updated_by = NULL,
                last_update = NOW() -- Update last_update to prevent further flushing
        ";

        // Execute the flush query
        if ($conn->query($flushQuery) === TRUE) {
            // echo "Inventory data for $inventoryDate ($inventoryDay) has been flushed successfully.";
        } else {
            // echo "Error flushing inventory data: " . $conn->error;
        }
    } else {
        // echo "Conditions for flushing data are not met. Data will not be flushed.<br>";
        if (empty($criteriaMessages)) {
            // echo "Waiting for tomorrow to flush the data.<br>";
        }
    }
} else {
    // If last update is from today, wait until tomorrow
    // echo "Last update is from today. Waiting until tomorrow to flush the data.<br>";
}

$conn->close();  // Close the database connection
