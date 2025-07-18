<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
error_reporting(1);

if (isset($_GET['invID'])) {
    $invID = $_GET['invID'];

    // Fetch invoice details
    include '../../connection/database.php';
    $query = "SELECT * FROM invoice WHERE invID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $invID);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();

    // Decode the orders JSON
    $orders = json_decode($invoice['orders'], true);

    // Calculate total number of items
    $totalItems = 0;
    foreach ($orders as $order) {
        $totalItems += $order['quantity'];
    }

    // Create the receipt-sized PDF
    $pdf = new FPDF('P', 'mm', array(80, 200)); // Receipt size
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Pizza Hut Chino Roces', 0, 1, 'C');

    // Header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, 'Sales Invoice', 0, 1, 'C');

    // Format the transaction date to "11/25/2024 5:30 PM"
    $transactionDate = new DateTime($invoice['transaction_date']);
    $formattedDate = $transactionDate->format('m/d/Y g:i A');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, 'Invoice # ' . $invoice['invID'], 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, 'Transaction Date: ' . $formattedDate, 0, 1, 'L');

    // Invoice Number below the transaction date

    $pdf->SetFont('Arial', '', 8);
    $pdf->Ln(1);

    // Walk-in == dine in
    if ($invoice['order_type'] == 'walk-in') {
        $invoice['order_type'] = 'dine in';
    }
    // Cashier and Order Type
    $pdf->Cell(0, 5, 'Cashier: ' . $invoice['cashier'], 0, 1);
    $pdf->Cell(0, 5, 'Transaction Type: ' . strtoupper($invoice['order_type']), 0, 1);
    $pdf->Cell(0, 5, 'Method of Payment: ' . strtoupper($invoice['mop']), 0, 1);
    $pdf->Ln(2);

    // Draw a straight line
    $pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
    $pdf->Ln(1);

    // Orders
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(40, 6, 'Qty  Item Description', 0, 0);
    $pdf->Cell(50, 6, 'Amount', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 8);

    foreach ($orders as $order) {
        // Item name and size
        $itemDetails = $order['quantity'] . 'x ' . $order['name'];
        $size = '(' . $order['size'] . ')';
        $price = 'PHP ' . number_format($order['price'], 2);

        $pdf->Cell(40, 5, $itemDetails, 0, 0);
        $pdf->Cell(50, 5, $price, 0, 1, 'L');
        $pdf->Cell(50, 1, $size, 0, 1); // Reduced height to 4 for tighter spacing
        $pdf->Ln(3);
    }

    // Draw a straight line
    $pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
    $pdf->Ln(3);

    // Show the number of items and total amount
    $pdf->SetFont('Arial', 'B', 10);
    // Calculate VAT and Vatable
    $vatable = $invoice['total_amount'] / 1.12; // Vatable amount
    $vat = $invoice['total_amount'] - $vatable; // VAT (12%)


    $financialDetails = [
        $totalItems . ' Item(s) Total AMOUNT' => 'PHP ' . number_format($invoice['total_amount'], 2),
        'Amount Tendered Cash' => 'PHP -' . number_format($invoice['amount_received'], 2),
        'CHANGE' => 'PHP ' . number_format($invoice['amount_change'], 2)
    ];

    $labelWidth = 40; // Fixed width for labels
    $amountWidth = 30; // Fixed width for amounts

    foreach ($financialDetails as $label => $value) {
        if ($label == 'Amount Tendered Cash' || $label == 'CHANGE' || $label == $totalItems . ' Item(s) Total AMOUNT') {
            $pdf->SetFont('Arial', 'B', 8); // Set font to bold for specific labels and values
        } else {
            $pdf->SetFont('Arial', '', 8); // Set font to regular for other labels and values
        }

        $pdf->Cell($labelWidth, 5, $label, 0, 0); // Label with the chosen font
        $pdf->Cell($amountWidth, 5, $value, 0, 1, 'L'); // Value with the chosen font, aligned to the right
    }
    $pdf->Ln(3);
    $pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
    $pdf->Ln(3);

    $vatable = $invoice['total_amount'] / 1.12; // Vatable amount
    $vat = $invoice['total_amount'] - $vatable; // VAT (12%)

    $financialDetails = [
        'Vatable Sales' => 'PHP ' . number_format($invoice['total_amount'], 2),
        'VAT (12%)' => 'PHP ' . number_format($vat, 2),
        'VAT Exempt Sales' => 'PHP ' . number_format(0, 2),
        'Zero Rated Sales' => 'PHP ' . number_format(0, 2),
    ];

    foreach ($financialDetails as $label => $value) {
        if ($label == 'Amount Tendered Cash' || $label == 'CHANGE') {
            $pdf->SetFont('Arial', 'B', 8); // Set font to bold for specific labels and values
        } else {
            $pdf->SetFont('Arial', '', 8); // Set font to regular for other labels and values
        }

        $pdf->Cell($labelWidth, 5, $label, 0, 0); // Label with the chosen font
        $pdf->Cell($amountWidth, 5, $value, 0, 1, 'L'); // Value with the chosen font, aligned to the right
    }
    $pdf->Ln(3);
    $pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
    $pdf->Ln(3);

    //total amount due
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(35, 5, 'Total Amount Due', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 5, 'PHP ' . number_format($invoice['total_amount'], 2), 0, 1, 'L');

    // Footer
    $pdf->Ln(6);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 6, 'Thank you for your purchase!', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Visit us again.', 0, 1, 'C');

    // Output PDF
    $pdf->Output();
    exit();
}
