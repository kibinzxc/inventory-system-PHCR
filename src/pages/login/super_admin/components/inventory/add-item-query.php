<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and format input
    $name = strtolower(trim($_POST['name'])); // Convert the name to lowercase
    $uom = isset($_POST['uom']) ? $_POST['uom'] : ''; // Assign the value of uom (unit of measurement)
    $beginning = isset($_POST['beginning']) ? (float)$_POST['beginning'] : 0; // Get beginning stock from the form and set default to 0

    // Set status based on beginning stock
    if ($beginning == 0) {
        $status = 'pending';
    } else {
        $status = 'pending'; // Default to in stock if beginning is greater than 0
    }

    // Input validation
    if (empty($name)) {
        header("Location: items.php?action=error&reason=name_empty&message=Item name cannot be empty.&name=$name&uom=$uom&beginning=$beginning");
        exit();
    } elseif (empty($uom)) {
        header("Location: items.php?action=error&reason=uom_empty&message=UOM cannot be empty.&name=$name&beginning=$beginning");
        exit();
    }

    // Check if item already exists in the database
    $checkSql = "SELECT COUNT(*) FROM inventory WHERE name = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('s', $name);
    $checkStmt->execute();
    $checkStmt->bind_result($existingItemCount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($existingItemCount > 0) {
        header("Location: items.php?action=error&reason=item_exists&message=$name already exists in the inventory.&name=$name&uom=$uom&beginning=$beginning");
        exit();
    }

    // Generate itemID
    $nameParts = explode(' ', $name); // Split the name into words
    $prefix = ''; // Initialize prefix

    // Create the prefix from the first letter of each word
    foreach ($nameParts as $part) {
        $prefix .= strtoupper(substr($part, 0, 1)); // Append the first letter of each word
    }

    $numWords = count($nameParts); // Count the number of words

    // Calculate how many random digits to add
    $digitsToAdd = max(6 - strlen($prefix), 0); // Ensure at least 0 digits if prefix is already 6 or more

    // Generate the suffix with random digits
    $suffix = str_pad(mt_rand(0, pow(10, $digitsToAdd) - 1), $digitsToAdd, '0', STR_PAD_LEFT); // Generate random digits

    // Concatenate to form the complete itemID
    $itemID = $prefix . $suffix;

    // Remove any non-alphanumeric characters from itemID (if any were generated accidentally)
    $itemID = preg_replace('/[^a-zA-Z0-9]/', '', $itemID);

    do {
        // Check if itemID already exists in the database
        $checkSql = "SELECT COUNT(*) FROM inventory WHERE itemID = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('s', $itemID);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            // If itemID exists, regenerate it
            $suffix = str_pad(mt_rand(0, pow(10, $digitsToAdd) - 1), $digitsToAdd, '0', STR_PAD_LEFT); // Generate new random digits
            $itemID = $prefix . $suffix; // Recreate the itemID with the new suffix
            $itemID = preg_replace('/[^a-zA-Z0-9]/', '', $itemID); // Remove non-alphanumeric characters again
        }
    } while ($count > 0); // Repeat until a unique itemID is generated


    // Retrieve the name of the current user from the database
    session_start();
    $userID = $_SESSION['user_id'];
    $userSql = "SELECT name FROM accounts WHERE uid = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param('i', $userID);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $userName = $userResult->fetch_assoc()['name'] ?? 'Unknown User'; // Fallback if user not found

    // Prepare SQL for inserting new item, including the uom and beginning stock
    $sql = "INSERT INTO inventory (itemID, name, uom, beginning, status, updated_by) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssdss', $itemID, $name, $uom, $beginning, $status, $userName);

    // Execute the insert operation
    if ($stmt->execute()) {
        header("Location: items.php?action=add&message=$name successfully added.");
        exit();
    } else {
        header("Location: items.php?action=error&reason=sql_failure&name=$name");
        exit();
    }

    $stmt->close();
}

$conn->close();
