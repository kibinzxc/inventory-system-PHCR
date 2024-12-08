<?php
include '../../connection/database.php';
error_reporting(E_ALL);
// Set the timezone
date_default_timezone_set('Asia/Manila');
// Get the current date in the format YYYY-MM-DD
$currentDate = date('Y-m-d');

// Select from usage_reports to get only the orders for today
$query = "SELECT * FROM item_reports WHERE DATE(date_inputted) = '$currentDate' ORDER BY date_inputted DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error executing query: " . $conn->error);
}
echo "  <link rel='stylesheet' href='recent-inv-card.css'>
        <div class='inv-orders-card'>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $invID = $row['invID'];
        $itemName = ucwords($row['name']);
        $itemQty = $row['qty'];
        $itemUom = $row['uom'];
        $dateInputted = $row['date_inputted'];


        // Calculate the time difference between the current date and the order date
        $orderTimestamp = strtotime($dateInputted);
        $currentTimestamp = time();
        $timeDifference = $currentTimestamp - $orderTimestamp;

        if ($timeDifference < 60) {
            $formattedTime = "Just now";
        } elseif ($timeDifference < 3600) {
            $minutes = floor($timeDifference / 60);
            $formattedTime = "$minutes mins ago";
        } else {
            $hours = floor($timeDifference / 3600);
            $formattedTime = "$hours " . ($hours == 1 ? "hour" : "hours") . " ago";
        }

        //if uom kg = KG, for pc if 1 = pc if more than 1 = pcs, if bt = bottle 1=bottle 1< = bottles
        if ($itemUom == 'kg') {
            $itemUom = 'KG';
        } else if ($itemUom == 'pc') {
            if ($itemQty == 1) {
                $itemUom = 'PC';
            } else {
                $itemUom = 'PCS';
            }
        } else if ($itemUom == 'bt') {
            if ($itemQty == 1) {
                $itemUom = 'Bottle';
            } else {
                $itemUom = 'Bottles';
            }
        }

        // Display the recent order card
        echo "
      
            <div class='inv-orders-row'>
            
                <div class='inv-details'>
                    <div class='inv-name'>
                        <span class='name'>$itemName</span>
                    </div>
                <span class='inv-type2'>Order ID#$invID</span>

                </div>
                <div class = 'inv-details inv-qty'>
                    <span class='inv-type'>$itemQty $itemUom</span>
                </div>
                <div class='inv-info inv-time'>
                    <div class='inv-date'>$formattedTime</div>
                </div>
            </div>";
    }
} else {
    echo "<p style='text-align:center;'>No recent updates yet</p>";
}
echo "</div>";
