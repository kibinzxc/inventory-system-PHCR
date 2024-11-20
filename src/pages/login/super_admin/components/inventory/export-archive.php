<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';

// Get the date parameter from the URL
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch data from the records_inventory table based on the given date
$sql = "SELECT name, itemID, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, usage_count, ending 
        FROM records_inventory 
        WHERE inventory_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();

// Initialize FPDF
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape, millimeters, A4 size

// Define the header for the table
function headerTable($pdf)
{
    // Add page number at the top
    $pdf->SetFont('Arial', 'I', 10); // Italic font for page number
    $pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 1, 'C'); // Center the page number

    // Set the font for the header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255); // Light blue background for headers
    $pdf->Cell(10, 10, '#', 1, 0, 'C', true); // Add the "#" column
    $pdf->Cell(72, 10, 'Name', 1, 0, 'L', true);
    $pdf->Cell(20, 10, 'Item ID', 1, 0, 'C', true);
    $pdf->Cell(15, 10, 'UOM', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Beginning', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Deliveries', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Transfers In', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Transfers Out', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Spoilage', 1, 0, 'C', true);
    $pdf->SetFillColor(255, 255, 153); // Highlight color for "Ending"
    $pdf->Cell(25, 10, 'Ending', 1, 0, 'C', true);
    $pdf->SetFillColor(200, 220, 255); // Light blue background for header
    $pdf->Cell(20, 10, 'Usage', 1, 1, 'C', true);
}

$pdf->AddPage();

// Add logo
$pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Adjust path, x, y, and size as needed

// Add title on the first page (centered)
$pdf->SetFont('Arial', 'B', 14);
$titleWidth = $pdf->GetStringWidth('Inventory record of ' . date('F j, Y', strtotime($date))) + 6; // Get the width of the title
$pdf->SetX((300 - $titleWidth) / 2); // Center the title (300 is page width in mm for A4 in landscape)
$pdf->Cell($titleWidth, 10, 'Inventory record of ' . date('F j, Y', strtotime($date)), 0, 0, 'C'); // Title centered

// Add current date generated text on the same line, aligned to the right
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date generated: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
$pdf->Ln(15); // Add some spacing

// Add table headers for the first page
headerTable($pdf);

// Add table rows
$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    $counter = 1; // Initialize row counter
    while ($row = $result->fetch_assoc()) {
        // Add data row
        $pdf->Cell(10, 10, $counter++, 1, 0, 'C'); // Increment and display row number
        $pdf->SetFont('Arial', 'B', 10); // Make the Name bold
        $pdf->Cell(72, 10, strtoupper($row['name']), 1, 0, 'L'); // Left-align and capitalize name
        $pdf->SetFont('Arial', '', 10); // Revert to regular font for the rest of the row
        $pdf->Cell(20, 10, $row['itemID'], 1, 0, 'C');
        $pdf->Cell(15, 10, strtoupper($row['uom']), 1, 0, 'C'); // Capitalize UOM
        $pdf->Cell(25, 10, $row['beginning'], 1, 0, 'C');
        $pdf->Cell(20, 10, $row['deliveries'], 1, 0, 'C');
        $pdf->Cell(25, 10, $row['transfers_in'], 1, 0, 'C');
        $pdf->Cell(25, 10, $row['transfers_out'], 1, 0, 'C');
        $pdf->Cell(20, 10, $row['spoilage'], 1, 0, 'C');

        // Highlight "Ending" in yellow and make bold
        $pdf->SetFillColor(255, 255, 153); // Yellow background for the "Ending" column
        $pdf->SetFont('Arial', 'B', 10); // Make "Ending" text bold
        $pdf->Cell(25, 10, $row['ending'], 1, 0, 'C', true); // Apply yellow fill

        $pdf->SetFillColor(200, 220, 255); // Reset to light blue fill for the next column
        $pdf->SetFont('Arial', '', 10); // Revert to regular font for the "Usage" column
        $pdf->Cell(20, 10, $row['usage_count'], 1, 1, 'C');
    }
    if ($pdf->GetY() > 150) {  // Adjust this value based on your row height and page layout
        $pdf->AddPage();
        // Add title on subsequent pages
        $pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Add logo
        $pdf->SetFont('Arial', 'B', 14);
        $titleWidth = $pdf->GetStringWidth('Inventory record of ' . date('F j, Y', strtotime($date))) + 6; // Get the width of the title
        $pdf->SetX((300 - $titleWidth) / 2); // Center the title (300 is page width in mm for A4 in landscape)
        $pdf->Cell($titleWidth, 10, 'Inventory record of ' . date('F j, Y', strtotime($date)), 0, 0, 'C'); // Title centered

        // Add current date generated text on the same line, aligned to the right
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Date generated: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
        $pdf->Ln(15); // Add some spacing

        // Add table headers for the first page
        headerTable($pdf);
    }
} else {
    $pdf->Cell(0, 10, 'No data available for the selected date', 1, 1, 'C');
}


// Output the PDF with the date-based filename
$filename = 'inventory-record of ' . $date . '.pdf'; // File name with the selected date
$pdf->Output('D', $filename); // D to force download
