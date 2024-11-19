<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';

$week = isset($_GET['week']) ? (int)$_GET['week'] : 1;

// Get the current date
$currentDate = new DateTime();

// Get the first day of the current month
$firstDayOfMonth = new DateTime("first day of this month");

// Find the first Monday of the month
$firstMonday = clone $firstDayOfMonth;
$firstMonday->modify('next monday');

// Function to calculate the start and end date for each week, starting from the first Monday
function getWeekRange($weekNumber, $firstMonday)
{
    $firstWeekStartDate = clone $firstMonday;
    $firstWeekStartDate->modify('+' . ($weekNumber - 1) . ' week'); // Calculate start date for the given week

    // Calculate the end date (6 days after the start date)
    $endDate = clone $firstWeekStartDate;
    $endDate->modify('+6 days');

    return [
        'start' => $firstWeekStartDate->format('F j, Y'),  // e.g., "November 4, 2024"
        'end' => $endDate->format('F j, Y')  // e.g., "November 10, 2024"
    ];
}

// Get the range for the selected week
$weekRange = getWeekRange($week, $firstMonday);

// Fetch data from the records_inventory table based on the week
$sql = "
    SELECT 
        CONCAT('Week ', LEAST(FLOOR((DAY(inventory_date) - 1) / 7) + 1, 4)) AS week_of_month,
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
        CONCAT('Week ', LEAST(FLOOR((DAY(inventory_date) - 1) / 7) + 1, 4)) = 'Week $week'
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
$titleWidth = $pdf->GetStringWidth('Weekly Inventory Report - Chino Roces') + 6; // Get the width of the title
$pdf->SetX((300 - $titleWidth) / 2); // Center the title (210 is the page width in mm for A4)
$pdf->Cell($titleWidth, 10, 'Weekly Inventory Report - Chino Roces', 0, 0, 'C'); // Title centered

// Add current date on the same line, aligned to the right
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
$pdf->Ln(10); // Line break before week range

// Display the week range below the title
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Week ' . $week . ' | ' . $weekRange['start'] . ' - ' . $weekRange['end'], 0, 1, 'C'); // Week range centered

$pdf->Ln(5); // Line break before table

// Add table headers for the first page
headerTable($pdf);

// Add table rows
$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    $counter = 1; // Initialize row counter
    while ($row = $result->fetch_assoc()) {
        // Calculate current inventory

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
        if ($pdf->GetY() > 150) {  // Adjust this value based on your row height and page layout
            $pdf->AddPage();
            // Add title on subsequent pages
            $pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Add logo
            $pdf->SetFont('Arial', 'B', 14);
            $titleWidth = $pdf->GetStringWidth('Weekly Inventory Report - Chino Roces') + 6; // Get the width of the title
            $pdf->SetX((300 - $titleWidth) / 2); // Center the title (210 is the page width in mm for A4)
            $pdf->Cell($titleWidth, 10, 'Weekly Inventory Report - Chino Roces', 0, 0, 'C'); // Title centered

            // Add current date on the same line, aligned to the right
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Date: ' . date('F j, Y'), 0, 0, 'R'); // Date aligned to the right
            $pdf->Ln(10); // Line break before week range

            // Display the week range below the title
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Week ' . $week . ' | ' . $weekRange['start'] . ' - ' . $weekRange['end'], 0, 1, 'C'); // Week range centered

            $pdf->Ln(5); // Line break before table
            headerTable($pdf); // Add the header again on the new page
        }
    }
} else {
    // If no data found, add a message
    $pdf->Cell(0, 10, 'No inventory records found for this week.', 0, 1, 'C');
}

// Output the PDF
//put the week number on the file name, e.g., report_week_1.pdf
$pdf->Output('D', 'inventory_report_week_' . $week . '.pdf');
