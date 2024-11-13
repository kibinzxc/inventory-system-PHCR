<?php
include '../../connection/database.php';

$query = isset($_GET['query']) ? strtolower($_GET['query']) : '';
$inventoryItems = [];

if ($query) {
    // Query to fetch items based on the search query
    $sql = "SELECT inventoryID, name FROM daily_inventory WHERE LOWER(name) LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $inventoryItems[] = $row;
    }

    $stmt->close();
}

$conn->close();

// Return results as JSON
echo json_encode($inventoryItems);
