<?php
include '../../connection/database.php';

session_start();
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

// Get the user_id from session to fetch the logged-in user's name
$user_id = $_SESSION['user_id']; // Assuming the user_id is stored in session after login

// SQL query to get the logged-in user's name
$sqlUser = "SELECT name FROM accounts WHERE uid = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$stmt->bind_result($user_name);
$stmt->fetch();
$stmt->close();

// Check if user_name was retrieved
if (!$user_name) {
    // Handle the error if the user name cannot be retrieved
    header("Location: items.php?action=error&message=User+not+found.");
    exit();
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
        usage_count, 
        ? AS submitted_by,  -- Use the logged-in user's name for submitted_by
        '$inventoryDate' AS inventory_date
    FROM daily_inventory
";

// Prepare and execute the insert query
$stmt = $conn->prepare($sqlInsert);
$stmt->bind_param('s', $user_name); // Bind the user's name as the submitted_by value

if ($stmt->execute()) {
    // Redirect to items.php with success message
    header("Location: items.php?action=success&message=Daily+inventory+report+submitted+successfully.");
    exit();
} else {
    // Redirect to items.php with error message
    header("Location: items.php?action=error&message=Error+submitting+daily+inventory+report.");
    exit();
}

// Close the connection
$conn->close();
