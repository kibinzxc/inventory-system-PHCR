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

    // Validate negative values
    if ($beginning < 0 || $transfers_in < 0 || $deliveries < 0 || $transfers_out < 0 || $spoilage < 0 || $ending < 0 || $usage < 0) {
        header("Location: items.php?action=error&reason=negative_input&message=Please+check+your+input%2C+negative+values+are+detected.&name=$name&beginning=$beginning&uom=$uom");
        exit();
    }

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
                header("Location: items.php?action=error&reason=user_not_found&message=User+not+found+or+session+expired");
                exit();
            }
            $stmt->close();
        } else {
            header("Location: items.php?action=error&reason=query_failed&message=Failed+to+retrieve+user+name");
            exit();
        }
    } else {
        header("Location: items.php?action=error&reason=not_logged_in&message=You+must+be+logged+in+to+update+inventory");
        exit();
    }

    // Fetch the current values from the database to compare
    $query = "SELECT name, itemID, beginning, uom, transfers_in, deliveries, transfers_out, spoilage, ending, usage_count FROM daily_inventory WHERE inventoryID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $inventoryID);
        $stmt->execute();
        $stmt->bind_result($current_name, $current_itemID, $current_beginning, $current_uom, $current_transfers_in, $current_deliveries, $current_transfers_out, $current_spoilage, $current_ending, $current_usage);
        $stmt->fetch();
        $stmt->close();

        // Check if any values have actually changed
        if (
            $name === $current_name &&
            $beginning == $current_beginning &&
            $uom === $current_uom &&
            $transfers_in == $current_transfers_in &&
            $deliveries == $current_deliveries &&
            $transfers_out == $current_transfers_out &&
            $spoilage == $current_spoilage &&
            $ending == $current_ending &&
            $usage == $current_usage
        ) {
            // If no changes, redirect with a message
            header("Location: items.php?action=error&reason=no_changes&message=No+changes+were+made+to+the+item.");
            exit();
        }

        // Generate a new itemID only if the name has changed
        if ($name !== $current_name) {
            $nameParts = explode(' ', $name);
            $prefix = '';

            foreach ($nameParts as $part) {
                $prefix .= strtoupper(substr($part, 0, 1));
            }

            $digitsToAdd = max(6 - strlen($prefix), 0);
            $suffix = str_pad(mt_rand(0, pow(10, $digitsToAdd) - 1), $digitsToAdd, '0', STR_PAD_LEFT);
            $itemID = $prefix . $suffix;
            $itemID = preg_replace('/[^a-zA-Z0-9]/', '', $itemID);

            // Ensure the new itemID is unique
            do {
                $checkIdSql = "SELECT COUNT(*) FROM daily_inventory WHERE itemID = ? AND inventoryID != ?";
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
        } else {
            // Keep the current itemID if the name hasn't changed
            $itemID = $current_itemID;
        }
    }

    // Determine stock status based on quantity
    $status = $beginning > 0 ? 'in stock' : 'out of stock';

    // Prepare the update query
    $query = "UPDATE daily_inventory SET name = ?, itemID = ?, beginning = ?, uom = ?, transfers_in = ?, deliveries = ?, transfers_out = ?, spoilage = ?, ending = ?, usage_count = ?, updated_by = ?, status = ? WHERE inventoryID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssdsdddddsssi', $name, $itemID, $beginning, $uom, $transfers_in, $deliveries, $transfers_out, $spoilage, $ending, $usage, $updated_by, $status, $inventoryID);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header("Location: items.php?action=success&message=$name+successfully+updated.");
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
        header("Location: items.php?action=error&reason=query_preparation_failed&message=Failed+to+prepare+query.");
        exit();
    }

    $conn->close();
}
