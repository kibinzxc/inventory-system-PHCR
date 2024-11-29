<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['spoilage'], $_POST['remarks'])) {
        $spoilage = $_POST['spoilage']; // Fetch spoilage data
        $remarks = $_POST['remarks']; // Fetch remarks data

        // Start a session to get the logged-in user's user_id
        session_start();
        $user_id = $_SESSION['user_id']; // Assuming the user_id is stored in session after login

        // Fetch the user's name based on user_id
        $query = "SELECT name FROM accounts WHERE uid = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($user_name);
            if (!$stmt->fetch()) {
                // Handle error if user not found
                header("Location: items.php?action=error&reason=user_not_found&message=User+not+found.");
                exit();
            }
            $stmt->close();
        } else {
            // Handle error if query fails
            header("Location: items.php?action=error&reason=query_failed&message=Failed+to+retrieve+user+name.");
            exit();
        }

        // Begin a transaction to ensure all updates happen together
        $conn->begin_transaction();

        try {
            $changesMade = false; // Track if any changes are made

            // Loop through each inventory item and update the spoilage, remarks, and updated_by fields
            foreach ($spoilage as $inventoryID => $spoilageAmount) {
                $spoilageAmount = (int) $spoilageAmount; // Ensure spoilage is an integer

                // Skip if no spoilage amount is entered (0 or empty)
                if ($spoilageAmount <= 0) {
                    continue;
                }

                // Ensure a remark exists for the corresponding inventoryID
                $remarkText = isset($remarks[$inventoryID]) ? $remarks[$inventoryID] : '';

                // If there's a new remark, update it without appending the old remark
                if ($remarkText != '') {
                    $updateSql = "UPDATE daily_inventory 
                                  SET spoilage = spoilage + ?, remarks = ?, updated_by = ? 
                                  WHERE inventoryID = ?";
                    $stmt = $conn->prepare($updateSql);
                    $stmt->bind_param("issi", $spoilageAmount, $remarkText, $user_name, $inventoryID);
                } else {
                    // If no new remark is provided, only update the spoilage and updated_by
                    $updateSql = "UPDATE daily_inventory 
                                  SET spoilage = spoilage + ?, updated_by = ? 
                                  WHERE inventoryID = ?";
                    $stmt = $conn->prepare($updateSql);
                    $stmt->bind_param("iss", $spoilageAmount, $user_name, $inventoryID);
                }

                // Execute the update query
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    // Update the 'ending' field by subtracting the spoilage
                    $updateEndingSql = "UPDATE daily_inventory 
                                        SET ending = ending - ? 
                                        WHERE inventoryID = ?";
                    $stmtEnding = $conn->prepare($updateEndingSql);
                    $stmtEnding->bind_param("ii", $spoilageAmount, $inventoryID);
                    $stmtEnding->execute();

                    $changesMade = true;
                }
            }

            // Commit the transaction
            $conn->commit();

            // Redirect based on whether any changes were made
            if ($changesMade) {

                header("Location: items.php?action=success&message=Spoilage+and+remarks+updated+successfully.");
            } else {
                header("Location: items.php?action=error&reason=no_changes&message=No+changes+were+made.");
            }
            exit();
        } catch (Exception $e) {
            // Rollback the transaction in case of any error
            $conn->rollback();

            // Redirect with error message
            header("Location: items.php?action=error&reason=update_failed&message=" . urlencode($e->getMessage()));
            exit();
        }
    }
}

// Close the connection
$conn->close();
