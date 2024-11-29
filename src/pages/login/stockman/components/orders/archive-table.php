<?php
include '../../connection/database.php';
error_reporting(1);
// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get current time
$currentDateTime = new DateTime();
$currentHour = $currentDateTime->format('H'); // Get current hour (24-hour format)

// Check if a 'date' parameter is passed in the URL
$selectedDate = isset($_GET['date']) ? $_GET['date'] : $inventoryDate; // Use passed date or default to current inventory date

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'invID';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Define valid columns for sorting and valid order directions
$valid_sort_columns = ['invID', 'order_type', 'transaction_date'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid sort column
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'invID';
// Ensure valid order direction
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// Query to fetch invoice data
$query = "SELECT id, invID, order_type, mop, transaction_date FROM invoice WHERE transaction_date LIKE ? 
          AND (invID LIKE ? OR order_type LIKE ? OR mop LIKE ?) 
          ORDER BY $sort $order";

// Prepare and execute the query
$selectedDateLike = $selectedDate . '%';  // Adding wildcard to match the date part only
$searchLike = '%' . $search . '%';  // Prepare the search pattern

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);  // Show error if prepare fails
}
$stmt->bind_param('ssss', $selectedDateLike, $searchLike, $searchLike, $searchLike);
$stmt->execute();
$result = $stmt->get_result();

// Check if no records are found
if ($result->num_rows === 0) {
    echo "<p style='text-align:center;'>No records found for the selected date.</p>";
} else {
    // Display the results
    while ($row = $result->fetch_assoc()) {
        $invID = $row['invID'];
        $order_type = $row['order_type'];
        $mop = $row['mop'];
        $transaction_date = $row['transaction_date'];

        // Format the transaction_date
        $transactionDateTime = new DateTime($transaction_date);
        $formattedTransactionDate = $transactionDateTime->format('F d, Y g:i A');

        // Echo the card HTML with dynamic data
        echo '<link rel="stylesheet" href="archive.css">';
        echo '<div class="card-container">';
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<div class="invoice-id">';
        echo '<p><strong>Invoice #</strong> ' . $invID . '</p>';
        echo '</div>';

        echo '<div class="invoice-info">';
        echo '<p><strong>Order Type:</strong> ' . strtoupper($order_type) . '</p>';
        echo '<p><strong>Method of Payment:</strong> ' . strtoupper($mop) . '</p>';
        echo '<p><strong>Transaction Date:</strong> ' . ($formattedTransactionDate) . '</p>';

        echo '</div>';

        echo '<div class="invoice-action">';
        echo '<button class="btn btn-invoice" onclick="generateInvoicePDF(' . $invID . ')">View Invoice <img src="../../assets/chevron-right.svg"></button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
<script>
    // Function to open a new window with the invoice PDF
    function generateInvoicePDF(invID) {
        var width = 100; // Width in mm (receipt size)
        var height = 200; // Height in mm (receipt size)

        // Convert to pixels (approximately)
        var widthPx = width * 3.7795275591; // Convert mm to pixels (1mm = 3.7795275591px)
        var heightPx = height * 3.7795275591;

        // Open the window with the specified size
        var newWindow = window.open('generate_invoice_pdf.php?invID=' + invID, '_blank', 'width=' + widthPx + ',height=' + heightPx);

        // Focus on the new window
        if (newWindow) {
            newWindow.focus();
        }
    }
</script>