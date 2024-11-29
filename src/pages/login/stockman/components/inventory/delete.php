<?php
include '../../connection/database.php';

if (isset($_GET['inventoryID'])) {
    $inventoryID = $_GET['inventoryID'];

    // First, retrieve the item name for the given inventoryID
    $sql_get_name = "SELECT name FROM daily_inventory WHERE inventoryID = ?";
    if ($stmt_get_name = $conn->prepare($sql_get_name)) {
        $stmt_get_name->bind_param("s", $inventoryID);

        if ($stmt_get_name->execute()) {
            $result = $stmt_get_name->get_result();
            $row = $result->fetch_assoc();

            // If the item exists, proceed to delete it
            if ($row) {
                $itemName = $row['name']; // Get the item name

                // Now, delete the item
                $sql_delete = "DELETE FROM daily_inventory WHERE inventoryID = ?";
                if ($stmt_delete = $conn->prepare($sql_delete)) {
                    $stmt_delete->bind_param("s", $inventoryID);

                    if ($stmt_delete->execute()) {
                        // Redirect with success message including item name
                        if (isset($_GET['scrollPos'])) {
                            $scrollPosition = $_GET['scrollPos'];
                        } else {
                            $scrollPosition = 0; // Default to top of the page if no scroll position is passed
                        }

                        header("Location: items.php?action=del&message=" . urlencode($itemName . " deleted successfully.") . "&scrollPos=" . urlencode($scrollPosition));
                        exit();
                    } else {
                        header("Location: items.php?action=error&reason=sql_failure");
                        exit();
                    }
                } else {
                    header("Location: items.php?action=error&reason=sql_failure");
                    exit();
                }
            } else {
                header("Location: items.php?action=error&reason=item_not_found");
                exit();
            }
        } else {
            header("Location: items.php?action=error&reason=sql_failure");
            exit();
        }
        $stmt_get_name->close();
    } else {
        header("Location: items.php?action=error&reason=sql_failure");
        exit();
    }

    $conn->close();
} else {
    header("Location: items.php?action=error&reason=no_item_specified");
    exit();
}
