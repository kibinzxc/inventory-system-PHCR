<?php
include '../../connection/database.php';

if (isset($_GET['itemID'])) {
    $itemID = $_GET['itemID'];

    $sql = "DELETE FROM items WHERE itemID = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $itemID);

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
