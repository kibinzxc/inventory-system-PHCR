<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';

// Fetch data from the daily_inventory table
$sql = "SELECT name, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, usage_count, ending, status FROM daily_inventory ORDER BY name";
$result = $conn->query($sql);

// Initialize FPDF
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape, millimeters, A4 size

// Override the Header method
$pdf->SetAutoPageBreak(true, 10); // Set auto page break with 10mm margin

function headerTable($pdf)
{
    // Add page number at the top
    $pdf->SetFont('Arial', 'I', 10); // Italic font for page number
    $pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 1, 'C'); // Center the page number

    // Set the font for the header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255); // Light blue background for headers
    $pdf->Cell(10, 10, '#', 1, 0, 'C', true); // Add the "#" column
    $pdf->Cell(85, 10, 'Name', 1, 0, 'L', true);
    $pdf->Cell(50, 10, 'Unit of Measurement', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Current Inventory', 1, 0, 'C', true); // Show current inventory instead of individual values
    $pdf->Cell(20, 10, 'Spoilage', 1, 0, 'C', true);

    $pdf->SetFillColor(255, 255, 153); // Highlight color for "Ending"
    $pdf->Cell(25, 10, 'Ending', 1, 0, 'C', true);
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(20, 10, 'Usage', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Status', 1, 1, 'C', true);
}

$pdf->AddPage();

// Add logo
$pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Adjust path, x, y, and size as needed

// Add title on the first page (centered)
$pdf->SetFont('Arial', 'B', 14);
$titleWidth = $pdf->GetStringWidth('Daily Inventory Summary Report - Chino Roces') + 6; // Get the width of the title
$pdf->SetX((300 - $titleWidth) / 2); // Center the title (210 is the page width in mm for A4)
$pdf->Cell($titleWidth, 10, 'Daily Inventory Summary Report - Chino Roces', 0, 0, 'C'); // Title centered

// Add current date on the same line, aligned to the right
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
// Add some spacing
$pdf->Ln(15);

// Add table headers for the first page
headerTable($pdf);

// Add table rows
$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    $counter = 1; // Initialize row counter
    while ($row = $result->fetch_assoc()) {
        // Calculate current inventory
        $current_inventory = $row["beginning"] + $row["deliveries"] + $row["transfers_in"] - $row["transfers_out"];

        // Remove ".00" if the value is a whole number
        $current_inventory = (strpos(number_format($current_inventory, 2), '.00') !== false) ? number_format($current_inventory, 0) : number_format($current_inventory, 2);

        // Add data row
        // Add row number
        $pdf->Cell(10, 10, $counter++, 1, 0, 'C'); // Increment and display row number

        // Capitalize and bold the name
        $pdf->SetFont('Arial', 'B', 10); // Set bold font for name
        $pdf->Cell(85, 10, strtoupper($row['name']), 1, 0, 'L'); // Left-align and capitalize name
        $pdf->SetFont('Arial', '', 10); // Reset to normal font for other columns

        // Capitalize and bold 'uom' field
        $pdf->SetFont('Arial', 'B', 10); // Set bold font for 'uom'
        $pdf->Cell(50, 10, strtoupper($row['uom']), 1, 0, 'C'); // Capitalized and bold 'uom', center-aligned
        $pdf->SetFont('Arial', '', 10); // Reset font to normal for next fields

        $pdf->Cell(40, 10, $current_inventory, 1, 0, 'C'); // Display current inventory, formatted to 2 decimal places

        // Highlight spoilage if greater than 1
        if ($row['spoilage'] > 1) {
            $pdf->SetFillColor(255, 204, 204); // Light Red for Spoilage > 1
        } else {
            $pdf->SetFillColor(255, 255, 255); // Default white for spoilage <= 1
        }
        $pdf->Cell(20, 10, $row['spoilage'], 1, 0, 'C', true);

        // Highlight Ending Inventory
        $pdf->SetFont('Arial', 'B', 10); // Set bold font for name

        $pdf->SetFillColor(255, 255, 153); // Light yellow for Ending
        $pdf->Cell(25, 10, $row['ending'], 1, 0, 'C', true);
        $pdf->SetFont('Arial', '', 10); // Set bold font for name

        $pdf->Cell(20, 10, $row['usage_count'], 1, 0, 'C');
        // Apply color only to the status column
        if ($row['status'] == "in stock") {
            $pdf->SetFillColor(200, 255, 200); // Light green
        } elseif ($row['status'] == "low stock") {
            $pdf->SetFillColor(255, 230, 180); // Light orange
        } elseif ($row['status'] == "out of stock") {
            $pdf->SetFillColor(255, 200, 200); // Light red
        } else {
            $pdf->SetFillColor(255, 255, 255); // Default white
        }

        // Set the background color for the status cell and make the text color match
        $pdf->Cell(30, 10, $row['status'], 1, 1, 'C', true);

        // Reset the text color after the status row
        $pdf->SetTextColor(0, 0, 0);

        if ($pdf->GetY() > 150) {  // Adjust this value based on your row height and page layout
            $pdf->AddPage();
            // Add title on subsequent pages
            $pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Add logo
            $pdf->SetFont('Arial', 'B', 14);
            $titleWidth = $pdf->GetStringWidth('Daily Inventory Summary Report - Chino Roces') + 6; // Get the width of the title
            $pdf->SetX((300 - $titleWidth) / 2); // Center the title (210 is the page width in mm for A4)
            $pdf->Cell($titleWidth, 10, 'Daily Inventory Summary Report - Chino Roces', 0, 0, 'C'); // Title centered
            // Add current date on the same line, aligned to the right
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Date: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
            $pdf->Ln(15); // Add some spacing before the header
            headerTable($pdf); // Add the header again on the new page
        }
    }
} else {
    $pdf->Cell(0, 10, 'No data available', 1, 1, 'C');
}

// Output the PDF
$filename = date('Y-m-d') . '_inventory-summary-report.pdf'; // File name with today's date
$pdf->Output('D', $filename); // D to force download