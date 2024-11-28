<?php
include '../../connection/database.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get the start and end dates for last week (Monday to Sunday)
$startOfLastWeek = date('Y-m-d H:i:s', strtotime('monday last week'));
$endOfLastWeek = date('Y-m-d 23:59:59', strtotime('sunday last week'));

// Query to fetch fast-moving product details from last week along with category
$sql = "SELECT ur.name, SUM(ur.quantity) AS total_quantity, ur.price, p.category
        FROM usage_reports ur
        JOIN products p ON ur.name = p.name
        WHERE ur.day_counted BETWEEN ? AND ?
        GROUP BY ur.name, ur.price, p.category
        ORDER BY total_quantity DESC
        LIMIT 5";  // Fetch the top 5 fast-moving products and sum quantities

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $startOfLastWeek, $endOfLastWeek);
$stmt->execute();
$result = $stmt->get_result();

// Initialize a counter for unavailable products and an array to hold unavailable product names
$unavailableCount = 0;
$unavailableProducts = [];

?>

<link rel="stylesheet" href="itemsTable.css">

<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th style='text-align: left;'>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Orders</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $counter = 1;
            while ($row = $result->fetch_assoc()) {
                // Check if the product is unavailable
                $productName = $row["name"];
                $statusCheckSql = "SELECT status FROM products WHERE name = ? AND status = 'not available'";
                $statusStmt = $conn->prepare($statusCheckSql);
                $statusStmt->bind_param('s', $productName);
                $statusStmt->execute();
                $statusResult = $statusStmt->get_result();

                // If the product is unavailable, increment the counter and add to unavailable products array
                if ($statusResult->num_rows > 0) {
                    $unavailableCount++;
                    $unavailableProducts[] = $productName;
                }

                // Add class for unavailable product
                $rowClass = ($statusResult->num_rows > 0) ? 'unavailable-product' : '';

                // Render the row with product name, category, total quantity (sum), price
                echo "<tr class='$rowClass'>";
                echo "<td>" . $counter++ . "</td>";
                echo "<td style='text-align: left;'><strong>" . strtoupper(htmlspecialchars($row["name"])) . "</strong></td>";
                echo "<td>" . strtoupper(htmlspecialchars($row["category"])) . "</td>";
                echo "<td>₱" . number_format($row["price"], 2) . "</td>";
                echo "<td><strong>" . intval($row["total_quantity"]) . "</strong></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No fast-moving products found for last week</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$conn->close();
?>

<!-- Modal HTML -->
<div class="warning-modal" id="warningModal">
    <div class="warning-modal-content">
        <span class="warning-icon">&#9888;</span> <!-- Warning Icon -->
        <div class="warning-message">
            <p>There are <strong id="unavailable-count"><?php echo $unavailableCount; ?></strong> unavailable fast-moving products.</p>
            <ul id="unavailable-products-list" style="text-align:center;"></ul> <!-- List for unavailable products -->
            <div class="warning-modal-buttons">
                <button class="btn-click-later" onclick="handleClickLater()">Remind Me Later</button>
                <button class="btn-reorder-now" onclick="handleReorderNow()">Reorder Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Add the modal styling and functionality -->
<style>
    /* Modal background */
    .warning-modal {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    /* Modal content */
    .warning-modal-content {
        background-color: #fff;
        padding: 20px 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        max-width: 600px;
        width: 100%;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Warning icon */
    .warning-icon {
        font-size: 5rem;
        color: #e0a800;
        margin-bottom: 5px;
    }

    /* Warning message styling */
    .warning-message {
        font-size: 1.2rem;
        color: #343434;
        font-weight: bold;
        margin-bottom: 20px;
    }

    /* Buttons wrapper */
    .warning-modal-buttons {
        display: flex;
        justify-content: space-around;
        width: 100%;
    }

    .warning-modal-buttons button {
        padding: 10px 20px;
        font-size: 1rem;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    .btn-reorder-now {
        background-color: #006D6D;
        /* Dark teal color */
        padding: 10px 20px;
        color: #ffffff;
        /* White text for contrast */
        border: none;
        font-size: 16px;
        font-weight: 500;
        margin-top: 20px;
    }

    .btn-reorder-now:hover {
        background-color: #005757;
        /* Slightly darker teal for hover */
        color: #e0f7f7;
        /* Lighter text on hover */
    }

    .btn-click-later {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #f1f1f1;
        color: #333;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        /* Dark gray border to match text */
    }

    .btn-click-later:hover {
        background-color: #e2e2e2;
        /* Slightly darker gray for hover */
        color: #212121;
        /* Even darker gray text on hover */
        border-color: #212121;
        /* Darker gray border on hover */
    }

    /* Modal visibility */
    .warning-modal.show {
        visibility: visible;
        opacity: 1;
    }
</style>

<script>
    // Function to show the modal
    function showModal() {
        const modal = document.getElementById('warningModal');
        modal.classList.add('show');
    }

    // Function to hide the modal
    function hideModal() {
        const modal = document.getElementById('warningModal');
        modal.classList.remove('show');
    }

    window.onload = function() {
        // Show the modal if there are unavailable products
        const unavailableCount = <?php echo $unavailableCount; ?>;
        const unavailableProducts = <?php echo json_encode($unavailableProducts); ?>;

        if (unavailableCount > 0) {
            // Display the list of unavailable products in the modal
            const productListElement = document.getElementById('unavailable-products-list');
            unavailableProducts.forEach(product => {
                const listItem = document.createElement('li');
                listItem.textContent = product;
                productListElement.appendChild(listItem);
            });

            if (sessionStorage.getItem('clickLater2')) {
                hideModal();
            } else {
                showModal();
            }
        }
    };

    // Button actions
    function handleReorderNow() {
        sessionStorage.setItem('clickLater2', 'true');
        window.location.href = 'https://my305028.s4hana.ondemand.com/ui?sap-language=EN&help-mixedLanguages=false&help-autoStartTour=PR_A8DA8C2F83492685#PurchaseRequisition-process&/?sap-iapp-state--history=TASXGJYIADHA21QZX0GGYQ5LYKJSIV3T2N87KL25Z'; // Redirect to items.php
        hideModal();
    }

    function handleClickLater() {
        sessionStorage.setItem('clickLater2', 'true');
        hideModal();
    }
</script>