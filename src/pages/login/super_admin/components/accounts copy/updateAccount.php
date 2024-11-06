<?php
include '../../connection/database.php'; // Make sure this path is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemID = $_POST['itemID'];
    $name = strtoupper(trim($_POST['name']));  // Capitalize item name
    $quantity = $_POST['qty'];
    $measurement = $_POST['measurement'];
    $status = $_POST['status'];



    // Prepare the SQL update query
    $sql = "UPDATE inventory SET name = ?, qty = ?, measurement = ?, status = ? WHERE itemID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Handle error in preparing statement
        header("Location: inventory.php?action=error&reason=sql_error&messages=Failed to prepare the SQL query.");
        exit();
    }

    // Bind parameters and execute
    $stmt->bind_param('sisii', $name, $quantity, $measurement, $status, $itemID);

    if ($stmt->execute()) {
        header("Location: accounts.php?action=success&reason=item_updated&messages=Item updated successfully.");
        exit();
    } else {
        header("Location: inventory.php?action=error&reason=sql_error&messages=Failed to update the item.");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: inventory.php?action=error&reason=invalid_request&messages=Invalid request.");
    exit();
}
