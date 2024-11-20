<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'deliveries' array is set
    if (isset($_POST['deliveries'])) {
        $deliveries = $_POST['deliveries'];

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

            // Loop through each inventory item and update the inventory table
            foreach ($deliveries as $inventoryID => $receivedAmount) {
                $receivedAmount = (int)$receivedAmount;

                // Skip if no quantity is entered (0 or empty)
                if ($receivedAmount <= 0) {
                    continue;
                }

                // Update the 'deliveries' column and 'updated_by' with the user's name
                $updateSql = "UPDATE daily_inventory 
                              SET deliveries = deliveries + ?, updated_by = ? 
                              WHERE inventoryID = ?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("isi", $receivedAmount, $user_name, $inventoryID);

                // Execute the update query
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $changesMade = true;

                    // Now update the 'ending' column based on deliveries and current ending value
                    $endingSql = "UPDATE daily_inventory 
                                  SET ending = ending + ? 
                                  WHERE inventoryID = ?";
                    $stmtEnding = $conn->prepare($endingSql);
                    $stmtEnding->bind_param("ii", $receivedAmount, $inventoryID);
                    $stmtEnding->execute();
                }
            }

            // Commit the transaction
            $conn->commit();

            // Redirect based on whether any changes were made
            if ($changesMade) {
                header("Location: items.php?action=success&message=Received+deliveries+submitted+successfully.");
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
