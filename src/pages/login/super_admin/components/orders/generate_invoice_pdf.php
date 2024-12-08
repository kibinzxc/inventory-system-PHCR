<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library

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
    $transactionDate = new DateTime($invoice['transaction_date']);
    $formattedDate = $transactionDate->format('m/d/Y g:i A');

    // Walk-in == dine in
    if ($invoice['order_type'] == 'walk-in') {
        $invoice['order_type'] = 'dine in';
        $staff = 'Cashier';
        $title = 'Transaction Date';
        $formattedOrderPlacedDate = $formattedDate;
    } elseif ($invoice['order_type'] == 'delivery') {
        $staff = 'Delivery Rider';

        //fetch the orderPlaced date from success_orders table 
        $query = "SELECT orderPlaced FROM success_orders WHERE orderID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $invoice['orderID']);
        $stmt->execute();
        $result = $stmt->get_result();
        $orderPlaced = $result->fetch_assoc();

        $orderPlacedDate = new DateTime($orderPlaced);
        $formattedOrderPlacedDate = $orderPlacedDate->format('m/d/Y g:i A');
        $title = 'Order Placed';
        $title2 = 'Delivery';
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

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, 'Invoice # ' . $invoice['invID'], 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, $title . ': ' . $formattedOrderPlacedDate, 0, 1, 'L');

    if ($invoice['order_type'] == 'delivery') {
        $pdf->Cell(0, 5, $title2 . ': ' . $formattedDate, 0, 1, 'L');
    }

    // Invoice Number below the transaction date

    $pdf->SetFont('Arial', '', 8);
    $pdf->Ln(1);

    //if method of payment == 0 show 'cash' 
    if ($invoice['mop'] == '0') {
        $invoice['mop'] = 'Cash';
    };
    // Cashier and Order Type
    $pdf->Cell(0, 5, $staff . ': ' . $invoice['cashier'], 0, 1);
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
        // Replace 'with' with 'w/' and remove extra spaces around the item name
        $itemName = str_replace(' with ', 'w/', $order['name']);
        $itemName = preg_replace('/\s+/', '', $itemName);  // Remove all spaces

        // Item name and size
        $itemDetails = $order['quantity'] . 'x ' . $itemName;
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



    if ($invoice['order_type'] == 'delivery') {
        // Define the delivery fee
        $deliveryFee = 65;

        // Subtract the delivery fee from the total amount to get the subtotal
        $subtotalAmount = $invoice['total_amount'] - $deliveryFee;

        // Calculate the total amount after adding the delivery fee back
        $totalAmountAfterDelivery = $subtotalAmount + $deliveryFee;

        // Prepare the financial details in the desired format
        $financialDetails = [
            $totalItems . ' Item(s) Total AMOUNT' => 'PHP '  . number_format($subtotalAmount, 2),
            'Delivery Fee' => 'PHP ' . number_format($deliveryFee, 2),
            'Amount Tendered Cash' => 'PHP ' . number_format($invoice['amount_received'], 2),
            'Total Amount' => 'PHP ' . number_format($totalAmountAfterDelivery, 2), // Total amount after adding the delivery fee back
            'CHANGE' => 'PHP ' . number_format($invoice['amount_change'], 2)

        ];
    } else {
        $financialDetails = [
            $totalItems . ' Item(s) Total AMOUNT' => 'PHP ' . number_format($invoice['total_amount'], 2),
            'Amount Tendered Cash' => 'PHP -' . number_format($invoice['amount_received'], 2),
            'CHANGE' => 'PHP ' . number_format($invoice['amount_change'], 2)
        ];
    }



    $labelWidth = 40; // Fixed width for labels
    $amountWidth = 30; // Fixed width for amounts

    foreach ($financialDetails as $label => $value) {

        if ($invoice['order_type'] == 'delivery') {
            //put here the subtotal, delivery fee, amount tendered, change, total amount
            if ($label == 'Amount Tendered Cash' || $label == 'CHANGE' || $label == $totalItems . ' Item(s) Total AMOUNT' || $label == 'Delivery Fee' || $label == 'Total Amount') {
                $pdf->SetFont('Arial', 'B', 8); // Set font to bold for specific labels and values
            } else {
                $pdf->SetFont('Arial', '', 8); // Set font to regular for other labels and values
            }
        } else {
            if ($label == 'Amount Tendered Cash' || $label == 'CHANGE' || $label == $totalItems . ' Item(s) Total AMOUNT') {
                $pdf->SetFont('Arial', 'B', 8); // Set font to bold for specific labels and values
            } else {
                $pdf->SetFont('Arial', '', 8); // Set font to regular for other labels and values
            }
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
        'Vatable Sales' => 'PHP ' . number_format($vatable, 2),
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
