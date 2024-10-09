<?php

include '../../connection/database.php';

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];

    // Prepare the SQL delete query
    $sql = "DELETE FROM accounts WHERE uid = ?";

    // Initialize a statement and execute the query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $uid); // Bind the 'uid' to the query

        // Execute the query
        if ($stmt->execute()) {
            echo "Account deleted successfully.";
        } else {
            echo "Error deleting account: " . $conn->error;
        }

        $stmt->close();
    }

    $conn->close();
} else {
    echo "No account specified.";
}
