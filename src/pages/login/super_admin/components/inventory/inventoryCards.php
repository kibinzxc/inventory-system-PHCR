<?php
include '../../connection/database.php';

$sql = "SELECT COUNT(*) AS item_count FROM inventory";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $itemCount = $row['item_count']; // Fetch the count from the result
} else {
    $itemCount = 0; // Default value if query fails
}

$sql = "
    SELECT 
        SUM(CASE WHEN status = 'in stock' THEN 1 ELSE 0 END) AS in_stock,
        SUM(CASE WHEN status = 'low stock' THEN 1 ELSE 0 END) AS low_stock,
        SUM(CASE WHEN status = 'out of stock' THEN 1 ELSE 0 END) AS out_of_stock
    FROM inventory
";

$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $inStock = $row['in_stock'];
    $lowStock = $row['low_stock'];
    $outOfStock = $row['out_of_stock'];
} else {
    $inStock = $lowStock = $outOfStock = 0; // Default to 0 if the query fails
}
?>

<link rel="stylesheet" href="inventoryCards.css">

<!-- <a href="https://www.flaticon.com/free-icons/product" title="product icons">Product icons created by Ida Desi Mariana - Flaticon</a> -->
<div class="item-container">
    <div class="item">
        <img src="../../assets/packaging.png" class="item-icon" alt="Items">
        <div class="item-details">
            <span class="item-count"><?php echo $itemCount; ?></span>
            <span class="item-label">Total Items</span>
        </div>
    </div>

    <!-- <a href="https://www.flaticon.com/free-icons/restaurant" title="restaurant icons">Restaurant icons created by Freepik - Flaticon</a> -->
    <div class="item">
        <img src="../../assets/cutlery.png" class="item-icon" alt="Products">
        <div class="item-details">
            <span class="item-count">0</span>
            <span class="item-label">Total Products</span>
        </div>
    </div>

    <!-- <a href="https://www.flaticon.com/free-icons/tick" title="tick icons">Tick icons created by Roundicons - Flaticon</a> -->
    <div class="item">
        <img src="../../assets/checked.png" class="item-icon" alt="available products">
        <div class="item-details">
            <span class="item-count">0</span>
            <span class="item-label">Available Products</span>
        </div>
    </div>

    <!-- <a href="https://www.flaticon.com/free-icons/inventory" title="inventory icons">Inventory icons created by Ida Desi Mariana - Flaticon</a>   -->
    <div class="item">
        <img src="../../assets/good-product.png" class="item-icon" alt="in stock">
        <div class="item-details">
            <span class="item-count"><?php echo $inStock; ?></span>
            <span class="item-label">In Stock Items</span>
        </div>
    </div>

    <!-- <a href="https://www.flaticon.com/free-icons/risk-management" title="risk-management icons">Risk-management icons created by Ida Desi Mariana - Flaticon</a> -->
    <div class="item">
        <img src="../../assets/risk-management.png" class="item-icon" alt="low stock">
        <div class="item-details">
            <span class="item-count"><?php echo $lowStock; ?></span>
            <span class="item-label">Low Stock Items</span>
        </div>
    </div>

    <!-- <a href="https://www.flaticon.com/free-icons/out-of-stock" title="out-of-stock icons">Out-of-stock icons created by Freepik - Flaticon</a> -->
    <div class="item">
        <img src="../../assets/nostock.png" class="item-icon" alt="out of stock">
        <div class="item-details">
            <span class="item-count"><?php echo $outOfStock; ?></span>
            <span class="item-label">Out of Stock Items</span>
        </div>
    </div>
</div>

<script src="items.js"></script>