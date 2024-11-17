<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['spoilage'], $_POST['remarks'])) {
        $spoilage = $_POST['spoilage'];
        $remarks = $_POST['remarks']; // Fetch remarks data

        // Begin a transaction to ensure all updates happen together
        $conn->begin_transaction();

        try {
            $changesMade = false; // Track if any changes are made

            // Loop through each inventory item and update the inventory table
            foreach ($spoilage as $inventoryID => $receivedAmount) {
                $receivedAmount = (int)$receivedAmount;

                // Skip if no quantity is entered (0 or empty)
                if ($receivedAmount <= 0) {
                    continue;
                }

                // Ensure a remark exists for the corresponding inventoryID
                $remarkText = isset($remarks[$inventoryID]) ? $remarks[$inventoryID] : '';

                // Prepare the SQL to update spoilage and remarks
                $updateSql = "UPDATE daily_inventory 
                              SET spoilage = spoilage + ?, remarks = CONCAT(IFNULL(remarks, ''), ?) 
                              WHERE inventoryID = ?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("isi", $receivedAmount, $remarkText, $inventoryID);

                // Execute the update query
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $changesMade = true;
                }
            }

            // Commit the transaction
            $conn->commit();

            // Redirect based on whether any changes were made
            if ($changesMade) {
                header("Location: items.php?action=success&message=Spoilage+report+submitted+successfully.");
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
