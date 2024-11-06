<?php
include '../../connection/database.php';

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];

    $sql = "DELETE FROM accounts WHERE uid = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $uid);

        if ($stmt->execute()) {
            header("Location: accounts.php?action=del&reason=account_deleted");
            exit();
        } else {
            header("Location: accounts.php?action=error&reason=sql_failure");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: accounts.php?action=error&reason=sql_failure");
        exit();
    }

    $conn->close();
} else {
    header("Location: accounts.php?action=error&reason=no_account_specified");
    exit();
}
