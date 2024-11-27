<?php
include '../../connection/database.php';

// Query to count the total number of products from the 'products' table
$sqlTotalProducts = "SELECT COUNT(*) AS totalProducts FROM products";
$stmtTotal = $conn->prepare($sqlTotalProducts);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$rowTotal = $resultTotal->fetch_assoc();
$totalProducts = $rowTotal['totalProducts'];

// Query to count the available products from the 'products' table
$sqlAvailable = "SELECT COUNT(*) AS availableProducts FROM products WHERE status = 'available'";
$stmtAvailable = $conn->prepare($sqlAvailable);
$stmtAvailable->execute();
$resultAvailable = $stmtAvailable->get_result();
$rowAvailable = $resultAvailable->fetch_assoc();
$availableProducts = $rowAvailable['availableProducts'];

// Query to count the not available products from the 'products' table
$sqlNotAvailable = "SELECT COUNT(*) AS notAvailableProducts FROM products WHERE status = 'not available'";
$stmtNotAvailable = $conn->prepare($sqlNotAvailable);
$stmtNotAvailable->execute();
$resultNotAvailable = $stmtNotAvailable->get_result();
$rowNotAvailable = $resultNotAvailable->fetch_assoc();
$notAvailableProducts = $rowNotAvailable['notAvailableProducts'];


$conn->close();
?>
<link rel="stylesheet" href="MainContent.css" />
<div id="main-content">

    <!-- Header Section -->
    <div class="container">
        <div class="header">
            <h1>Product Reports & Analytics</h1>
            <div class="btn-wrapper">
                <!-- <a href="week-overview.php" target="_blank" class="btn"><img src="../../assets/external-link.svg" alt=""> Online Orders</a>-->
                <a href="order-logs.php" class="btn"><img src="../../assets/file-text.svg" alt=""> Download</a>
            </div>
        </div>
        <div class="btncontents">
            <!-- <a href="https://www.flaticon.com/free-icons/increase" title="increase icons">Increase icons created by pojok d - Flaticon</a> -->
            <a href="#" class="active"><img src="../../assets/cutlery.png" class="img-btn-link">Products</a>
            <a href="ingredients.php"><img src="../../assets/text-file.png" class="img-btn-link">Ingredients</a>
            <a href="sales.php"><img src="../../assets/graph.png" class="img-btn-link">Sales</a>
        </div>
        <br>
        <div class="inventory-cards">
            <!-- Total Products Card -->
            <a href="../inventory/product-preview.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h5>Total Products</h5>
                            <p class="sales-amount"><?php echo number_format($totalProducts); ?></p>
                        </div>
                        <div class="percentage-box">
                            <div class="percentage-body">
                                <div class="neutral">
                                    <img src="../../assets/cutlery.png" alt="" style="width:50px; margin-right:5px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>


            <!-- Available Products Card -->
            <a href="../inventory/product-preview.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h5>Available Products</h5>
                            <p class="sales-amount"><?php echo number_format($availableProducts); ?></p>
                        </div>
                        <div class="percentage-box">
                            <div class="percentage-body">
                                <div class="neutral">
                                    <img src="../../assets/available.png" alt="" style="width:50px; margin-right:5px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Not Available Products Card -->
            <a href="../inventory/product-preview.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h5>Not Available Products</h5>
                            <p class="sales-amount"><?php echo number_format($notAvailableProducts); ?></p>
                        </div>
                        <div class="percentage-box">
                            <div class="percentage-body">
                                <div class="neutral">
                                    <img src="../../assets/nostock1.png" alt="" style="width:50px; margin-right:5px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="same_column-container">
            <div class="same-column">
                <div class="table_container daily_sales">
                    <h3>Weekly Fast-Moving Products</h3>
                    <?php include 'top-selling.php' ?>
                </div>
                <div class="table_container daily_sales2">
                    <h3>Weekly Slow-Moving Product</h3>
                    <?php include 'least-selling.php' ?>
                </div>
            </div>
            <div class="table_container paddington">
                <div class="header">
                    <h3>Product Analysis</h3>
                    <div class="btn-wrapper">
                        <?php
                        // Check if the 'product' parameter exists in the URL
                        if (isset($_GET['product'])) {
                        ?>
                            <div class="btn-wrapper download">
                                <a href="export-product-analysis.php?product=<?php echo $_GET['product']; ?>" class="btn">
                                    <img src="../../assets/file-text.svg" alt=""> Download
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <?php include 'product-analysis.php' ?>
            </div>
        </div>
        <br>

    </div>
</div>