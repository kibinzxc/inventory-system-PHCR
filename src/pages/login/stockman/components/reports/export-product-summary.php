<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';
Error_reporting(1);

// Query to get the total number of products
$totalProductsSql = "SELECT COUNT(*) AS total FROM products";
$totalProductsResult = $conn->query($totalProductsSql);
$totalProductsRow = $totalProductsResult->fetch_assoc();
$totalProducts = $totalProductsRow['total'];

// Query to get the available products list
$availableProductsSql = "SELECT name FROM products WHERE status = 'available'";
$availableProductsResult = $conn->query($availableProductsSql);
$availableProducts = [];
while ($row = $availableProductsResult->fetch_assoc()) {
    $availableProducts[] = $row['name'];
}

// Query to get the not available products list
$notAvailableProductsSql = "SELECT name FROM products WHERE status = 'not available'";
$notAvailableProductsResult = $conn->query($notAvailableProductsSql);
$notAvailableProducts = [];
while ($row = $notAvailableProductsResult->fetch_assoc()) {
    $notAvailableProducts[] = $row['name'];
}

// Calculate the start and end date for last week (Monday to Sunday 12:59 PM)
$currentDate = new DateTime();
$currentDate->setISODate($currentDate->format('Y'), $currentDate->format('W'));
$startOfLastWeek = $currentDate->modify('last Monday')->format('Y-m-d');
$endOfLastWeek = $currentDate->modify('next Sunday')->setTime(23, 59)->format('Y-m-d H:i');

// Query to get the top 5 fast-moving products from last week
$fastMovingSql = "
    SELECT name, SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE day_counted >= ? AND day_counted <= ?
    GROUP BY name
    ORDER BY total_quantity DESC
    LIMIT 5
";
$stmtFastMoving = $conn->prepare($fastMovingSql);
$stmtFastMoving->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
$stmtFastMoving->execute();
$resultFastMoving = $stmtFastMoving->get_result();
$fastMovingProducts = [];
while ($row = $resultFastMoving->fetch_assoc()) {
    $fastMovingProducts[] = ['name' => $row['name'], 'quantity' => $row['total_quantity']];
}

// Query to get the top 5 slow-moving products from last week
$slowMovingSql = "
    SELECT name, SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE day_counted >= ? AND day_counted <= ?
    GROUP BY name
    ORDER BY total_quantity ASC
    LIMIT 5
";
$stmtSlowMoving = $conn->prepare($slowMovingSql);
$stmtSlowMoving->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
$stmtSlowMoving->execute();
$resultSlowMoving = $stmtSlowMoving->get_result();
$slowMovingProducts = [];
while ($row = $resultSlowMoving->fetch_assoc()) {
    $slowMovingProducts[] = ['name' => $row['name'], 'quantity' => $row['total_quantity']];
}

class PDF extends FPDF
{
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Create PDF instance
$pdf = new PDF();
$pdf->AliasNbPages(); // Required for total page count
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
// Add Pizza Hut logo
$pdf->Image('../../assets/logo-black.png', 75, 10, 50); // Adjust the path and size as needed
// Add "Date Generated" text
$pdf->SetXY(10, 15); // Move to the right of the logo
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date Generated: ' . date('Y-m-d'), 0, 1);

// Add title
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Product Summary Report', 0, 1, 'C');
$pdf->Ln(10);

// Total number of products
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Total Number of Products: ' . $totalProducts, 0, 1);
$pdf->Ln(5);

// Available products list
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Available Products:', 0, 1);
$pdf->SetFont('Arial', '', 12);
foreach ($availableProducts as $product) {
    $pdf->Cell(0, 10, '- ' . $product, 0, 1);
}
$pdf->Ln(5);

// Not available products list
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Not Available Products:', 0, 1);
$pdf->SetFont('Arial', '', 12);
foreach ($notAvailableProducts as $product) {
    $pdf->Cell(0, 10, '- ' . $product, 0, 1);
}
$pdf->Ln(10);

// Add a new page for weekly fast-moving and slow-moving products
$pdf->AddPage();
$pdf->Image('../../assets/logo-black.png', 75, 10, 50); // Adjust the path and size as needed
$pdf->Ln(20);
// Weekly Fast-moving Products Table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Weekly Fast-moving Products:', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 10, 'Product Name', 1, 0, 'C');
$pdf->Cell(95, 10, 'Quantity', 1, 0, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
foreach ($fastMovingProducts as $product) {
    $pdf->Cell(95, 10, $product['name'], 1, 0, 'C');
    $pdf->Cell(95, 10, $product['quantity'], 1, 0, 'C');
    $pdf->Ln();
}
$pdf->Ln(10);

// Weekly Slow-moving Products Table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Weekly Slow-moving Products:', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 10, 'Product Name', 1, 0, 'C');
$pdf->Cell(95, 10, 'Quantity', 1, 0, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
foreach ($slowMovingProducts as $product) {
    $pdf->Cell(95, 10, $product['name'], 1, 0, 'C');
    $pdf->Cell(95, 10, $product['quantity'], 1, 0, 'C');
    $pdf->Ln();
}
$pdf->Ln(10);
// Extend FPDF class to add footer with page numbers


// Add your content...


$pdf->Output('D', date('Y-m-d') . '-Product_Summary_Report.pdf'); // I: Display the PDF in the browser, D: Download the PDF
