<?php
include '../../connection/database.php';
error_reporting(0);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Fetch the current time from Google server
function getGoogleServerTime()
{
    $url = "http://www.google.com"; // You can use other websites too

    // Get the headers from the response
    $headers = get_headers($url, 1);

    // Check if the Date header is available
    if (isset($headers['Date'])) {
        $dateHeader = $headers['Date'];  // The date header contains the time from the server
        $googleTime = strtotime($dateHeader);  // Convert to Unix timestamp

        // Return formatted time and date
        return date('Y-m-d H:i:s', $googleTime); // Example: '2024-11-18 05:19:00'
    }
    return false;
}

// Get the time from Google's server
$googleServerTime = getGoogleServerTime();

// If we successfully retrieved the time from Google
if ($googleServerTime) {
    $currentDateTime = new DateTime($googleServerTime);  // Create DateTime object from Google time
    $currentTime = $currentDateTime->format('H:i');  // Get current time (HH:MM)
    $currentDate = $currentDateTime->format('Y-m-d');  // Get current date (YYYY-MM-DD)
} else {
    // Fallback to the server's system time if the Google time couldn't be fetched
    $currentDateTime = new DateTime();
    $currentTime = $currentDateTime->format('H:i');
    $currentDate = $currentDateTime->format('Y-m-d');
}

// Get the current timezone
$currentTimezone = date_default_timezone_get();

// Display current time, date, and timezone
echo "Current time: " . $currentTime . "<br>";
echo "Current date: " . $currentDate . "<br>";
echo "Current timezone: " . $currentTimezone . "<br>";

// Define the start time for checking updates (6:00 AM)
$startOfDay = new DateTime('today 06:00 AM'); // 6:00 AM today
$startOfDayStr = $startOfDay->format('Y-m-d H:i:s'); // Format it as string for query

// Step 1: Check last_update in daily_inventory
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
$lastUpdateTime = $lastUpdate->format('H:i'); // Extract time part
$lastUpdateDate = $lastUpdate->format('Y-m-d'); // Extract date part

// Echo the last_update details
echo "Last update time: " . $lastUpdateTime . "<br>";
echo "Last update date: " . $lastUpdateDate . "<br>";

// Step 2: Determine which date to check based on last_update time
if ($lastUpdateDate < $currentDate) {
    // If last_update date is before the current date
    if ($lastUpdateTime < '06:00') {
        // If last_update was before 6:00 AM, use the day before yesterday
        $yesterdayDate = $lastUpdate->modify('-1 day')->format('Y-m-d');
    } else {
        // Otherwise, use yesterday's date
        $yesterdayDate = $lastUpdateDate;
    }
} else {
    // If the last update is today
    $yesterdayDate = $currentDate;
}

echo "Determined date to check: " . $yesterdayDate . "<br>";

// Step 3: Check if there are records for the determined date (yesterdayDate) in records_inventory
$checkInventoryQuery = "
    SELECT COUNT(*) AS recordCount
    FROM records_inventory
    WHERE inventory_date = '$yesterdayDate';
";

// Execute the query to check for records in records_inventory
$inventoryResult = $conn->query($checkInventoryQuery);
$inventoryRow = $inventoryResult->fetch_assoc();
$inventoryCount = $inventoryRow['recordCount'];

// Step 4: Check if there are updates in daily_inventory since the last check time
$checkUpdatesQuery = "
    SELECT COUNT(*) AS updateCount
    FROM daily_inventory
    WHERE last_update >= '$startOfDayStr'
        AND last_update < NOW();  -- Check if updates were made after 6:00 AM today
";

// Execute the query to check for updates
$updateResult = $conn->query($checkUpdatesQuery);
$updateRow = $updateResult->fetch_assoc();
$updateCount = $updateRow['updateCount'];

// Step 5: Logic to flush data
if ($updateCount == 0) {
    // No updates found in daily_inventory since 6:01 AM today
    if ($inventoryCount > 0) {
        // If there are records for the previous day, proceed with flush
        $flushQuery = "
            UPDATE daily_inventory
            SET 
                beginning = ending,   
                ending = beginning,
                deliveries = 0,
                transfers_in = 0,
                transfers_out = 0,
                spoilage = 0,
                remarks = NULL,
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
            echo "Inventory data for $yesterdayDate has been flushed successfully.";
        } else {
            echo "Error flushing inventory data: " . $conn->error;
        }
    } else {
        // If no records for the previous day, do not flush
        echo "No records for $yesterdayDate in records_inventory. Data will not be flushed.";
    }
} else {
    // If there were updates after 6:00 AM, do not flush
    echo "There were updates after 6:00 AM today. Data will not be flushed.";
}

$conn->close();  // Close the database connection
