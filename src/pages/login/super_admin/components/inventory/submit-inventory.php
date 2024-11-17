<?php
include '../../connection/database.php';

// Set your timezone (if needed)
date_default_timezone_set('Asia/Manila'); // Replace with your desired timezone

// Get the current date and time
$currentDateTime = new DateTime();
$currentHour = $currentDateTime->format('H'); // Get the current hour in 24-hour format

// Check if the current time is before 6:00 AM
if ($currentHour < 6) {
    // Set the date to the previous day
    $inventoryDate = $currentDateTime->modify('-1 day')->format('Y-m-d');
} else {
    // Set the date to the current day
    $inventoryDate = $currentDateTime->format('Y-m-d');
}

// Get the item IDs from the daily_inventory table
$sqlCheck = "
    SELECT itemID 
    FROM records_inventory
    WHERE inventory_date = '$inventoryDate'
";

$resultCheck = $conn->query($sqlCheck);

if ($resultCheck->num_rows > 0) {
    // A record with the same inventory_date and itemID exists
    header("Location: items.php?action=error&message=Inventory already submitted for the same date.");
    exit();
}

// SQL query to insert data from daily_inventory to records_inventory, including the inventory_date
$sqlInsert = "
    INSERT INTO records_inventory (
        itemID, 
        name, 
        uom, 
        beginning, 
        deliveries, 
        transfers_in, 
        transfers_out, 
        spoilage, 
        ending, 
        remarks, 
        usage_count, 
        submitted_by,
        inventory_date
    )
    SELECT 
        itemID, 
        name, 
        uom, 
        beginning, 
        deliveries, 
        transfers_in, 
        transfers_out, 
        spoilage, 
        ending, 
        remarks, 
        usage_count, 
        updated_by AS submitted_by,
        '$inventoryDate' AS inventory_date
    FROM daily_inventory
";

// Execute the query
if ($conn->query($sqlInsert) === TRUE) {
    // Redirect to items.php with success message
    header("Location: items.php?action=success&message=Daily+inventory+report+submitted+successfully.");
    exit();
} else {
    // Redirect to items.php with error message
    header("Location: items.php?action=error&message=Error+submitting+daily+inventory+report.");
    exit();
}
