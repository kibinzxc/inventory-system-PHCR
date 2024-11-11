<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../connection/database.php';

    // Retrieve form data
    $inventoryID = $_POST['inventoryID'];
    $name = strtolower(trim($_POST['name'])); // Convert the name to lowercase
    $beginning = $_POST['beginning'];
    $uom = $_POST['uom'];
    $transfers_in = $_POST['transfers_in'];
    $deliveries = $_POST['deliveries'];
    $transfers_out = $_POST['transfers_out'];
    $spoilage = $_POST['spoilage'];
    $ending = $_POST['ending'];
    $usage = $_POST['usage_count'];



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

    // Check if name already exists, excluding the current item
    $checkNameSql = "SELECT COUNT(*) FROM inventory WHERE name = ? AND inventoryID != ?";
    $checkStmt = $conn->prepare($checkNameSql);
    $checkStmt->bind_param('si', $name, $inventoryID);
    $checkStmt->execute();
    $checkStmt->bind_result($existingItemCount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($existingItemCount > 0) {
        header("Location: items.php?action=error&reason=name_exists&message=Item+name+already+exists&name=$name&beginning=$beginning&uom=$uom");
        exit();
    }

    // Generate itemID based on the updated name
    $nameParts = explode(' ', $name);
    $prefix = '';

    foreach ($nameParts as $part) {
        $prefix .= strtoupper(substr($part, 0, 1));
    }

    $digitsToAdd = max(6 - strlen($prefix), 0);
    $suffix = str_pad(mt_rand(0, pow(10, $digitsToAdd) - 1), $digitsToAdd, '0', STR_PAD_LEFT);
    $itemID = $prefix . $suffix;
    $itemID = preg_replace('/[^a-zA-Z0-9]/', '', $itemID);

    // Ensure the new itemID is unique, excluding the current item
    do {
        $checkIdSql = "SELECT COUNT(*) FROM inventory WHERE itemID = ? AND inventoryID != ?";
        $checkIdStmt = $conn->prepare($checkIdSql);
        $checkIdStmt->bind_param('si', $itemID, $inventoryID);
        $checkIdStmt->execute();
        $checkIdStmt->bind_result($count);
        $checkIdStmt->fetch();
        $checkIdStmt->close();

        if ($count > 0) {
            $suffix = str_pad(mt_rand(0, pow(10, $digitsToAdd) - 1), $digitsToAdd, '0', STR_PAD_LEFT);
            $itemID = $prefix . $suffix;
            $itemID = preg_replace('/[^a-zA-Z0-9]/', '', $itemID);
        }
    } while ($count > 0);

    // Determine stock status based on quantity
    $status = $beginning > 0 ? 'in stock' : 'out of';

    // Prepare the update query to include new fields
    $query = "UPDATE inventory SET name = ?, itemID = ?, beginning = ?, uom = ?, transfers_in = ?, deliveries = ?, transfers_out = ?, spoilage = ?, ending = ?, usage_count = ?,  updated_by = ?, status = ? WHERE inventoryID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssdsdddddsssi', $name, $itemID, $beginning, $uom, $transfers_in, $deliveries, $transfers_out, $spoilage, $ending, $usage, $updated_by, $status, $inventoryID);

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
