<?php
session_start(); // Start the session
include 'connection/database-conn.php';
include 'connection/database-db.php';
// Check if dish_id is provided in the URL
if (isset($_GET['remove_item'])) {
    $dish_id = $_GET['remove_item'];



    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve current quantity and price
    $select_sql = "SELECT qty, price FROM cart WHERE dish_id = ?";
    $select_stmt = $conn->prepare($select_sql);
    $select_stmt->bind_param("i", $dish_id);
    $select_stmt->execute();
    $select_stmt->bind_result($quantity, $price);
    $select_stmt->fetch();
    $select_stmt->close();

    if ($quantity > 1) {
        // If quantity is more than 1, decrement it
        $update_sql = "UPDATE cart SET qty = qty - 1, totalprice = (qty) * price WHERE dish_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $dish_id);

        if ($update_stmt->execute()) {
            $_SESSION['success'] = "Bag updated successfully";
        } else {
            $_SESSION['sucesss'] = "Error" . $update_stmt->error;
        }

        $update_stmt->close();
    } elseif ($quantity === 1) {
        // If quantity is 1, remove the data
        $delete_sql = "DELETE FROM cart WHERE dish_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $dish_id);

        if ($delete_stmt->execute()) {
            $_SESSION['success'] = "Item removed successfully";
        } else {
            $_SESSION['success'] = "Error removing item: " . $delete_stmt->error;
        }

        $delete_stmt->close();
    } else {
        $_SESSION['success'] = "Invalid value";
    }

    // Close the database connection
    $conn->close();
} else {
    $_SESSION['message'] = "No dish_id provided.";
}

// Redirect to another page (change 'your_page.php' to your desired page)
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
