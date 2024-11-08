<?php
include '../../connection/database.php';

if (isset($_GET['inventoryID'])) {
    $inventoryID = $_GET['inventoryID'];

    $sql = "DELETE FROM inventory WHERE inventoryID = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $inventoryID);

        if ($stmt->execute()) {
            header("Location: items.php?action=del&reason=ingredient_deleted");
            exit();
        } else {
            header("Location: items.php?action=error&reason=sql_failure");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: items.php?action=error&reason=sql_failure");
        exit();
    }

    $conn->close();
} else {
    header("Location: items.php?action=error&reason=no_item_specified");
    exit();
}
