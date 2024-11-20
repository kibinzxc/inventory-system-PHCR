<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../connection/database.php';

    // Retrieve form data
    $recordID = $_POST['recordID'];
    $beginning = $_POST['beginning'];
    $uom = $_POST['uom'];
    $transfers_in = $_POST['transfers_in'];
    $deliveries = $_POST['deliveries'];
    $transfers_out = $_POST['transfers_out'];
    $spoilage = $_POST['spoilage'];
    $ending = $_POST['ending'];
    $usage = $_POST['usage_count'];

    // Validate negative values
    if ($beginning < 0 || $transfers_in < 0 || $deliveries < 0 || $transfers_out < 0 || $spoilage < 0 || $ending < 0 || $usage < 0) {
        $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
        $parsedUrl = parse_url($refererUrl);
        parse_str($parsedUrl['query'], $queryParams);

        // Preserve the "date" parameter in the URL
        if (isset($queryParams['date'])) {
            $date = $queryParams['date'];
            $refererUrl = "archive.php?date=$date";  // Maintain only the date parameter
        }

        header("Location: $refererUrl&action=error&reason=negative_input&message=Please+check+your+input%2C+negative+values+are+detected.&beginning=$beginning&uom=$uom");
        exit();
    }

    // Start session to get the logged-in user's ID
    session_start();

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Fetch the user's name
        $query = "SELECT name FROM accounts WHERE uid = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($submitted_by);
            if (!$stmt->fetch()) {
                $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
                $parsedUrl = parse_url($refererUrl);
                parse_str($parsedUrl['query'], $queryParams);

                // Preserve the "date" parameter in the URL
                if (isset($queryParams['date'])) {
                    $date = $queryParams['date'];
                    $refererUrl = "archive.php?date=$date";  // Maintain only the date parameter
                }

                header("Location: $refererUrl&action=error&reason=user_not_found&message=User+not+found+or+session+expired");
                exit();
            }
            $stmt->close();
        } else {
            $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
            $parsedUrl = parse_url($refererUrl);
            parse_str($parsedUrl['query'], $queryParams);

            // Preserve the "date" parameter in the URL
            if (isset($queryParams['date'])) {
                $date = $queryParams['date'];
                $refererUrl = "archive.php?date=$date";  // Maintain only the date parameter
            }

            header("Location: $refererUrl&action=error&reason=query_failed&message=Failed+to+retrieve+user+name");
            exit();
        }
    } else {
        $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
        $parsedUrl = parse_url($refererUrl);
        parse_str($parsedUrl['query'], $queryParams);

        // Preserve the "date" parameter in the URL
        if (isset($queryParams['date'])) {
            $date = $queryParams['date'];
            $refererUrl = "archive.php?date=$date";  // Maintain only the date parameter
        }

        header("Location: $refererUrl&action=error&reason=not_logged_in&message=You+must+be+logged+in+to+update+inventory");
        exit();
    }

    // Fetch the current values from the database to compare
    $query = "SELECT beginning, uom, transfers_in, deliveries, transfers_out, spoilage, ending, usage_count FROM records_inventory WHERE recordID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $recordID);
        $stmt->execute();
        $stmt->bind_result($current_beginning, $current_uom, $current_transfers_in, $current_deliveries, $current_transfers_out, $current_spoilage, $current_ending, $current_usage);
        if ($stmt->fetch()) {
            // Add debug output
            error_log("Current Data: Beginning: $current_beginning, UOM: $current_uom, Transfers In: $current_transfers_in, Deliveries: $current_deliveries");
        } else {
            error_log("No record found for recordID: $recordID");
            $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
            $parsedUrl = parse_url($refererUrl);
            parse_str($parsedUrl['query'], $queryParams);

            // Preserve the "date" parameter in the URL
            if (isset($queryParams['date'])) {
                $date = $queryParams['date'];
                $refererUrl = "archive.php?date=$date";  // Maintain only the date parameter
            }

            header("Location: $refererUrl&action=error&reason=record_not_found&message=Record+not+found+for+ID:$recordID");
            exit();
        }
        $stmt->close();
    }

    // Check if any values have actually changed
    if (
        $beginning == $current_beginning &&
        $uom === $current_uom &&
        $transfers_in == $current_transfers_in &&
        $deliveries == $current_deliveries &&
        $transfers_out == $current_transfers_out &&
        $spoilage == $current_spoilage &&
        $ending == $current_ending &&
        $usage == $current_usage
    ) {
        // If no changes, redirect with a message
        $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
        $parsedUrl = parse_url($refererUrl);
        parse_str($parsedUrl['query'], $queryParams);

        // Preserve the "date" parameter in the URL
        if (isset($queryParams['date'])) {
            $date = $queryParams['date'];
            $refererUrl = "archive.php?date=$date";  // Maintain only the date parameter
        }

        header("Location: $refererUrl&action=error&reason=no_changes&message=No+changes+were+made+to+the+item");
        exit();
    }

    // Prepare the update query
    $query = "UPDATE records_inventory SET beginning = ?, uom = ?, transfers_in = ?, deliveries = ?, transfers_out = ?, spoilage = ?, ending = ?, usage_count = ?, submitted_by = ? WHERE recordID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('dsdssdddsi', $beginning, $uom, $transfers_in, $deliveries, $transfers_out, $spoilage, $ending, $usage, $submitted_by, $recordID);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Parse the referer URL and extract the date part
                $refererUrl = $_SERVER['HTTP_REFERER'];
                $parsedUrl = parse_url($refererUrl);
                parse_str($parsedUrl['query'], $queryParams);

                // Get the date parameter (assuming it's the "date" key in the query string)
                if (isset($queryParams['date'])) {
                    $date = $queryParams['date'];
                    $refererUrl = "archive.php?date=$date";  // Only use the archive.php?date= part
                }

                header("Location: $refererUrl&action=success&message=Record+successfully+updated");
                exit();
            } else {
                $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
                header("Location: $refererUrl&action=error&reason=no_changes&message=No+changes+made");
                exit();
            }
        } else {
            error_log("Error executing query: " . $stmt->error); // Log the error
            $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
            header("Location: $refererUrl&action=error&reason=query_failed&message=Failed+to+update+the+item");
            exit();
        }

        $stmt->close();
    } else {
        error_log("Failed to prepare query: " . $conn->error); // Log the error
        $refererUrl = $_SERVER['HTTP_REFERER']; // Get the previous URL
        header("Location: $refererUrl&action=error&reason=query_failed&message=Failed+to+prepare+the+update+query");
        exit();
    }
}
