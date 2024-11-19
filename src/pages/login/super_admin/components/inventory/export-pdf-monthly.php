<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';

// Get the current date
$currentDate = new DateTime();

// Get the first day of the current month
$firstDayOfMonth = new DateTime("first day of this month");

// Get the last day of the current month
$lastDayOfMonth = new DateTime("last day of this month");

// Get the selected month and year from GET parameters, or default to current month and year
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)$currentDate->format('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)$currentDate->format('Y');

// Fetch data from the records_inventory table based on the month and year
$sql = "
    SELECT 
        name,
        itemID,
        uom,
        MIN(beginning) AS beginning,
        SUM(deliveries) AS deliveries,
        SUM(transfers_in) AS transfers_in,
        SUM(transfers_out) AS transfers_out,
        SUM(spoilage) AS spoilage,
        MAX(ending) AS ending,
        (
            MIN(beginning) + SUM(deliveries) + SUM(transfers_in) 
            - MAX(ending) - SUM(transfers_out) - SUM(spoilage)
        ) AS usage_count
    FROM records_inventory
    WHERE 
        MONTH(inventory_date) = $month 
        AND YEAR(inventory_date) = $year
    GROUP BY name, itemID, uom
    ORDER BY name ASC";
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
    $pdf->Cell(70, 10, 'Name', 1, 0, 'L', true);
    $pdf->Cell(15, 10, 'UoM', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Beginning Inventory', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Deliveries', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Transfers In', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Transfers Out', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Spoilage', 1, 0, 'C', true);
    $pdf->SetFillColor(255, 255, 153); // Light yellow for Ending
    $pdf->Cell(35, 10, 'Ending Inventory', 1, 0, 'C', true);
    $pdf->SetFillColor(200, 220, 255); // Light blue for Usage
    $pdf->Cell(15, 10, 'Usage', 1, 1, 'C', true);
}

$pdf->AddPage();

// Add logo
$pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Adjust path, x, y, and size as needed

// Add title on the first page (centered)
$pdf->SetFont('Arial', 'B', 14);
$titleWidth = $pdf->GetStringWidth('Monthly Inventory Report - Chino Roces') + 6; // Get the width of the title
$pdf->SetX((300 - $titleWidth) / 2); // Center the title (210 is the page width in mm for A4)
$pdf->Cell($titleWidth, 10, 'Monthly Inventory Report - Chino Roces', 0, 0, 'C'); // Title centered

// Add current date on the same line, aligned to the right
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
$pdf->Ln(10); // Line break before month range

// Display the month range below the title
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Month: ' . date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $year, 0, 1, 'C'); // Month and year centered

$pdf->Ln(5); // Line break before table

// Add table headers for the first page
headerTable($pdf);

// Add table rows
$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    $counter = 1; // Initialize row counter
    while ($row = $result->fetch_assoc()) {
        // Add data row
        $pdf->Cell(10, 10, $counter++, 1, 0, 'C'); // Increment and display row number
        $pdf->SetFont('Arial', 'B', 10); // Set bold font for name
        $pdf->Cell(70, 10, strtoupper($row['name']), 1, 0, 'L'); // Left-align and capitalize name
        $pdf->SetFont('Arial', '', 10); // Reset to normal font for other columns

        // Capitalize and bold 'uom' field
        $pdf->SetFont('Arial', 'B', 10); // Set bold font for 'uom'
        $pdf->Cell(15, 10, strtoupper($row['uom']), 1, 0, 'C'); // Capitalized and bold 'uom', center-aligned
        $pdf->SetFont('Arial', '', 10); // Reset font to normal for next fields

        // Add Beginning, Deliveries, Transfers In, and Transfers Out
        $pdf->Cell(40, 10, $row['beginning'], 1, 0, 'C');
        $pdf->Cell(20, 10, $row['deliveries'], 1, 0, 'C');
        $pdf->Cell(25, 10, $row['transfers_in'], 1, 0, 'C');
        $pdf->Cell(25, 10, $row['transfers_out'], 1, 0, 'C');

        // Highlight spoilage if greater than 1
        if ($row['spoilage'] > 1) {
            $pdf->SetFillColor(255, 204, 204); // Light Red for Spoilage > 1
        } else {
            $pdf->SetFillColor(255, 255, 255); // Default white for spoilage <= 1
        }
        $pdf->Cell(20, 10, $row['spoilage'], 1, 0, 'C', true);

        // Highlight Ending Inventory
        $pdf->SetFillColor(255, 255, 153); // Light yellow for Ending
        $pdf->SetFont('Arial', 'B', 10); // Set bold font for name
        $pdf->Cell(35, 10, $row['ending'], 1, 0, 'C', true);

        $pdf->Cell(15, 10, $row['usage_count'], 1, 1, 'C');
        // Reset the text color after the status row
        $pdf->SetTextColor(0, 0, 0);

        if ($pdf->GetY() > 150) {  // Adjust this value based on your row height and page layout
            $pdf->AddPage();
            // Add title on subsequent pages
            $pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Add logo
            $pdf->SetFont('Arial', 'B', 14);
            $titleWidth = $pdf->GetStringWidth('Monthly Inventory Report - Chino Roces') + 6; // Get the width of the title
            $pdf->SetX((300 - $titleWidth) / 2); // Center the title (210 is the page width in mm for A4)
            $pdf->Cell($titleWidth, 10, 'Monthly Inventory Report - Chino Roces', 0, 0, 'C'); // Title cent
            // Add current date on the same line, aligned to the right
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Date: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
            $pdf->Ln(10); // Add some spacing before the header
            // Display the month range below the title
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Month: ' . date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $year, 0, 1, 'C'); // Month and year centered

            $pdf->Ln(5); // Line break before table
            headerTable($pdf); // Add the header again on the new page
        }
    }
} else {
    // If no data found, add a message
    $pdf->Cell(0, 10, 'No inventory records found for this month.', 0, 1, 'C');
}

// Output the PDF
// Generate file name with the month and year
$filename = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-monthly-inventory-report.pdf'; // File name with year and month
$pdf->Output('D', $filename); // D to force download
