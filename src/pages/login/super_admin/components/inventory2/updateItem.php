<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../connection/database.php';

    // Retrieve form data
    $inventoryID = $_POST['inventoryID'];
    $qty = $_POST['qty'];
    $measurement = $_POST['measurement'];

    // Start session to get the logged-in user's ID
    session_start();

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Fetch the user's name
        $query = "SELECT name FROM accounts WHERE uid = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($updated_by);
            if (!$stmt->fetch()) {
                header("Location: items.php?action=error&reason=user_not_found&message=User+not+found+or+session+expired&qty=$qty");
                exit();
            }
            $stmt->close();
        } else {
            header("Location: items.php?action=error&reason=query_failed&message=Failed+to+retrieve+user+name&qty=$qty");
            exit();
        }
    } else {
        header("Location: items.php?action=error&reason=not_logged_in&message=You+must+be+logged+in+to+update+inventory&qty=$qty");
        exit();
    }

    // Validate quantity input

    if ($qty < 0) {
        header("Location: items.php?action=error&reason=qty_invalid&message=Invalid+input.");
        exit();
    }

    // Check inventoryID
    $checkQuery = "SELECT inventoryID FROM inventory WHERE inventoryID = ?";
    if ($stmt = $conn->prepare($checkQuery)) {
        $stmt->bind_param('i', $inventoryID);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            header("Location: items.php?action=error&reason=inventoryID_not_found&message=Item+not+found.");
            exit();
        }
        $stmt->close();
    }

    // Determine stock status based on quantity
    if ($qty == 0) {
        $status = 'out of stock';
    } elseif ($qty >= 1 && $qty < 5) {
        $status = 'low stock';
    } else {
        $status = 'in stock';
    }

    // Prepare the update query with the new status field
    $query = "UPDATE inventory SET qty = ?, measurement = ?, updated_by = ?, status = ? WHERE inventoryID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssssi', $qty, $measurement, $updated_by, $status, $inventoryID);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header("Location: items.php?success=1");
                exit();
            } else {
                header("Location: items.php?action=error&reason=no_changes&message=No+changes+made.");
                exit();
            }
        } else {
            header("Location: items.php?action=error&reason=query_failed&message=Failed+to+update+the+item.");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: items.php?action=error&reason=query_preparation_failed&message=Failed+to+prepare+query.&qty=$qty");
        exit();
    }

    $conn->close();
}
