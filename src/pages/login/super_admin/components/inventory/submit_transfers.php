<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if both transfers_in and transfers_out are set
    if (isset($_POST['transfers_in']) && isset($_POST['transfers_out'])) {
        $transfers_in = $_POST['transfers_in'];
        $transfers_out = $_POST['transfers_out'];

        // Begin a transaction to ensure all updates happen together
        $conn->begin_transaction();

        try {
            $changesMade = false; // Track if any changes are made

            // Loop through each inventory item and update the inventory table
            foreach ($transfers_in as $inventoryID => $inAmount) {
                $inAmount = (int)$inAmount;
                $outAmount = isset($transfers_out[$inventoryID]) ? (int)$transfers_out[$inventoryID] : 0;

                // Update the 'transfers_in' and 'transfers_out' columns for each item
                $updateSql = "UPDATE inventory 
                              SET transfers_in = transfers_in + ?, transfers_out = transfers_out + ? 
                              WHERE inventoryID = ?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("iii", $inAmount, $outAmount, $inventoryID);

                // Execute the update query
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $changesMade = true;
                }
            }

            // Commit the transaction
            $conn->commit();

            // Redirect based on whether any changes were made
            if ($changesMade) {
                header("Location: items.php?action=success&message=Transfers+report+submitted+successfully.");
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
