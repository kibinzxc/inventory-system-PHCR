<?php
include '../../connection/database.php';

// Query to update the status of each item in daily_inventory based on the 'ending' value
$sql = "UPDATE daily_inventory
        SET status = CASE
            WHEN ending = 0 THEN 'out of stock'
            WHEN ending > 5 THEN 'in stock'
            WHEN ending > 0 AND ending <= 5 THEN 'low stock'
            ELSE status
        END";

$stmt = $conn->prepare($sql);

$stmt->execute();
