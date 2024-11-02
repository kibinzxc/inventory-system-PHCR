<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and format input
    $name = ucwords(trim($_POST['name'])); // Capitalize the first letter of each word
    $shelfLife = isset($_POST['shelfLife']) ? (int)$_POST['shelfLife'] : 0;

    // Input validation
    if (empty($name)) {
        header("Location: items.php?action=error&reason=name_empty&message=Item name cannot be empty.&name=$name&shelfLife=$shelfLife");
        exit();
    } elseif ($shelfLife <= 0) {
        header("Location: items.php?action=error&reason=shelfLife_invalid&message=Invalid Input.&name=$name");
        exit();
    }

    // Generate a unique itemID
    $itemID = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

    // Retrieve the name of the current user from the database
    session_start();
    $userID = $_SESSION['user_id'];
    $userSql = "SELECT name FROM accounts WHERE uid = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param('i', $userID);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $userName = $userResult->fetch_assoc()['name'] ?? 'Unknown User'; // Fallback if user not found

    // Prepare SQL for inserting new item
    $sql = "INSERT INTO items (itemID, name, shelfLife, addedBy) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssis', $itemID, $name, $shelfLife, $userName);

    // Execute the insert operation
    if ($stmt->execute()) {
        header("Location: items.php?action=add&message=Item successfully added.");
        exit();
    } else {
        header("Location: items.php?action=error&reason=sql_failure&name=$name");
        exit();
    }

    $stmt->close();
}

$conn->close();
