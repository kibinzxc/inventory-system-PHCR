<?php
include '../../connection/database.php';

// Start session to get the logged-in user's user_id
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if both transfers_in and transfers_out are set
    if (isset($_POST['transfers_in']) && isset($_POST['transfers_out'])) {
        $transfers_in = $_POST['transfers_in'];
        $transfers_out = $_POST['transfers_out'];

        // Get the user_id from session
        $user_id = $_SESSION['user_id']; // Assuming the user_id is stored in session after login

        // Fetch the user's name based on user_id
        $query = "SELECT name FROM accounts WHERE uid = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('s', $user_id);
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
            foreach ($transfers_in as $inventoryID => $inAmount) {
                $inAmount = (int)$inAmount;
                $outAmount = isset($transfers_out[$inventoryID]) ? (int)$transfers_out[$inventoryID] : 0;

                // Check if transfers_out exceeds the ending stock
                $checkStockSql = "SELECT ending FROM daily_inventory WHERE inventoryID = ?";
                $stmt = $conn->prepare($checkStockSql);
                $stmt->bind_param('i', $inventoryID);
                $stmt->execute();
                $stmt->bind_result($endingStock);
                if (!$stmt->fetch()) {
                    // Handle error if inventory item not found
                    header("Location: items.php?action=error&reason=inventory_not_found&message=Inventory+item+not+found.");
                    exit();
                }
                $stmt->close();

                // If transfers_out is greater than ending stock, show an error
                if ($outAmount > $endingStock) {
                    // Insufficient stock error
                    header("Location: transfers-report.php?action=error&reason=insufficient_stock&message=Insufficient+stock.");
                    exit();
                }

                // Prepare the update query with a condition to only update 'updated_by' if a change occurred
                $updateSql = "UPDATE daily_inventory 
                              SET 
                                  transfers_in = transfers_in + ?, 
                                  transfers_out = transfers_out + ?, 
                                  ending = ending + ? - ?, 
                                  updated_by = CASE WHEN (transfers_in + ?) <> transfers_in OR (transfers_out + ?) <> transfers_out THEN ? ELSE updated_by END 
                              WHERE inventoryID = ? AND ((transfers_in + ?) <> transfers_in OR (transfers_out + ?) <> transfers_out)";

                // Bind parameters for the prepared statement
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param('iiiiiisiii', $inAmount, $outAmount, $inAmount, $outAmount, $inAmount, $outAmount, $user_name, $inventoryID, $inAmount, $outAmount);

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
