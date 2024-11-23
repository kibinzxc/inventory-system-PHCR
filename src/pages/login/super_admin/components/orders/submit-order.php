<?php
// Get the raw POST data (JSON format)
$data = json_decode(file_get_contents("php://input"), true);

// Extract the orders and other data
$orders = $data['orders'];  // Array of orders
$cash = $data['cash'];
$change = $data['change'];

// Start the session and get the user ID
session_start();
$user_id = $_SESSION['user_id']; // Assuming the logged-in user's ID is stored in the session

include '../../connection/database.php';

// Get the name of the user from the 'accounts' table using the user ID
$query_user = "SELECT name FROM accounts WHERE uid = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);  // Bind the user ID to the query
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    // Fetch the name of the user
    $row_user = $result_user->fetch_assoc();
    $user_name = $row_user['name'];
} else {
    // If no user is found, handle the error (you could redirect or show an error message)
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

// Calculate the total amount from the order
$total_amount = 0;
foreach ($orders as $order) {
    $total_amount += $order['price'] * $order['quantity'];
}

// Generate the invID using current date and the next available counter
$today = date('mdY'); // Current date in MMDDYYYY format

// Prepare the query to find the latest invoice for today
$query = "SELECT invID FROM invoice WHERE invID LIKE '$today%' ORDER BY invID DESC LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Get the most recent invID
    $row = $result->fetch_assoc();
    $last_invID = $row['invID'];

    // Extract the numeric part and increment it
    $last_number = (int)substr($last_invID, -3); // Get last 3 digits
    $next_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT); // Increment and pad with zeros
} else {
    // No invoices today, start with 001
    $next_number = '001';
}

// Combine current date with the next number for invID
$invID = $today . $next_number;

// Start a transaction
$conn->begin_transaction();

try {
    // Convert the orders array to a JSON string
    $orderJson = json_encode($orders);

    // Prepare an SQL statement to insert the invoice data
    $stmt = $conn->prepare("INSERT INTO invoice (invID, orders, total_amount, amount_received, amount_change, order_type, cashier) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddsss", $invID, $orderJson, $total_amount, $cash, $change, $order_type, $cashier);

    // Set the 'order_type' and 'cashier'
    $order_type = 'walk-in';
    $cashier = $user_name; // Use the logged-in user's name from the accounts table

    // Execute the query
    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    // Send a success response with the generated invID
    echo json_encode(['success' => true, 'invID' => $invID]);
} catch (Exception $e) {
    // Rollback in case of error
    $conn->rollback();

    // Send a failure response
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Close the connection
$conn->close();
