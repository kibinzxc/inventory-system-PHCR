<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'deliveries' array is set
    if (isset($_POST['deliveries'])) {
        $deliveries = $_POST['deliveries'];

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

                // Update the 'deliveries' column for each item in the inventory
                $updateSql = "UPDATE daily_inventory 
                              SET deliveries = deliveries + ? 
                              WHERE inventoryID = ?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("ii", $receivedAmount, $inventoryID);

                // Execute the update query
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $changesMade = true;
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
