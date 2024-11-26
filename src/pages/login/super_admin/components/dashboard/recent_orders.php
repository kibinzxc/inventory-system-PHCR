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

// Query to fetch invoice data
$query = "SELECT id, invID, order_type, mop, transaction_date FROM invoice ORDER BY transaction_date DESC LIMIT 2";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);  // Show error if prepare fails
}

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
        echo '<link rel="stylesheet" href="MainCOntent.css">';
        echo '<div class="custom-card-container" onclick="generateInvoicePDF(' . $invID . ')">';  // Set the onclick on the outer div
        echo '<div class="custom-card">';  // Updated class name
        echo '<div class="custom-card-body">';  // Updated class name
        echo '<div class="custom-invoice-id">';  // Updated class name
        echo '<p>Invoice #' . $invID . '</p>';
        echo '</div>';

        echo '<div class="custom-invoice-info">';  // Updated class name
        echo '<p><strong>Order Type:</strong> ' . strtoupper($order_type) . '</p>';
        echo '<p><strong>Method of Payment:</strong> ' . strtoupper($mop) . '</p>';
        echo '<p><strong>Transaction Date:</strong> ' . ($formattedTransactionDate) . '</p>';

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