<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';
error_reporting(0);

// Function to calculate the average orders per day for the last week
function getAverageOrdersPerDay($conn, $productName)
{
    $currentDate = new DateTime();
    $currentDate->setISODate($currentDate->format('Y'), $currentDate->format('W'));
    $startOfLastWeek = $currentDate->modify('last Monday')->format('Y-m-d');
    $endOfLastWeek = $currentDate->modify('next Sunday')->setTime(23, 59)->format('Y-m-d H:i');

    $sql = "
        SELECT SUM(quantity) AS total_quantity
        FROM usage_reports
        WHERE name = ? AND day_counted >= ? AND day_counted <= ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $productName, $startOfLastWeek, $endOfLastWeek);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalOrdersLastWeek = $row['total_quantity'] ?? 0;

    return ($totalOrdersLastWeek > 0) ? round($totalOrdersLastWeek / 7) : 0;
}

// Get all products
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$ingredientThresholds = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get product ingredients
        $ingredients = json_decode($row['ingredients'], true);

        // Calculate the average orders per day and low stock threshold for the product
        $averageOrdersPerDay = getAverageOrdersPerDay($conn, $row['name']);
        $lowStockThreshold = $averageOrdersPerDay * 3;

        foreach ($ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['ingredient_name']); // Ensure consistent case
            $ingredientQuantity = floatval($ingredient['quantity']);
            $ingredientMeasurement = $ingredient['measurement'];

            // Calculate the total quantity needed for the ingredient based on the low stock threshold
            $totalQuantityNeeded = $ingredientQuantity * $lowStockThreshold;

            // Add the quantity needed to the ingredient thresholds array
            if (!isset($ingredientThresholds[$ingredientName])) {
                $ingredientThresholds[$ingredientName] = ['quantity' => 0, 'measurement' => $ingredientMeasurement];
            }
            $ingredientThresholds[$ingredientName]['quantity'] += $totalQuantityNeeded;
        }
    }
} else {
    echo '<p>No products found.</p>';
}

$stmt->close();
$conn->close();

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Logo
        $this->Image('../../assets/logo-black.png', 80, 10, 50);
        // Date generated
        $this->SetFont('Arial', '', 12);
        $this->SetXY(10, 10);
        $this->Cell(0, 10, 'Date Generated: ' . date('Y-m-d'), 0, 1);
        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Sort ingredient thresholds alphabetically by ingredient name
ksort($ingredientThresholds);

// Create PDF
$pdf = new PDF();
$pdf->AliasNbPages(); // Required for total page count
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add title
$pdf->Cell(0, 10, 'Ingredients Low Stock Threshold Report', 0, 1, 'C');
$pdf->Ln(10);

// Ingredients Table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 10, 'Ingredient Name', 1, 0, 'C');
$pdf->Cell(95, 10, 'Low Stock Threshold', 1, 0, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);

foreach ($ingredientThresholds as $ingredientName => $data) {
    $pdf->Cell(95, 10, ucfirst($ingredientName), 1, 0, 'C');
    $quantity = $data['quantity'];
    $measurement = $data['measurement'];

    // Convert grams to kg if the measurement is grams
    if ($measurement === 'grams') {
        $quantity = $quantity / 1000; // Convert grams to kg
        $measurement = 'kg'; // Update measurement to kg
    }

    // Remove unnecessary .00
    $quantity = rtrim(rtrim(number_format($quantity, 2), '0'), '.');

    // Format and display the quantity with measurement
    if ($measurement === 'kg') {
        $pdf->Cell(95, 10, "$quantity kg", 1, 0, 'C');
    } elseif ($measurement === 'pc' || $measurement === 'pcs') {
        $pdf->Cell(95, 10, "$quantity pcs", 1, 0, 'C');
    } elseif ($measurement === 'bottle') {
        $pdf->Cell(95, 10, "$quantity bottle" . ($quantity > 1 ? 's' : ''), 1, 0, 'C');
    } else {
        $pdf->Cell(95, 10, "$quantity $measurement", 1, 0, 'C');
    }
    $pdf->Ln();
}

$pdf->Output('D', 'low-stock-threshold-report-' . date('Y-m-d') . '.pdf');
