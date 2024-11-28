<?php
include '../../connection/database.php';
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../phpmailer/src/Exception.php';
require '../../../phpmailer/src/PHPMailer.php';
require '../../../phpmailer/src/SMTP.php';


// Function to calculate the average orders per day for the last week
function getAverageOrdersPerDay($conn, $productName)
{
    $currentDate = new DateTime();
    $currentDate->setISODate($currentDate->format('Y'), $currentDate->format('W'));
    $startOfLastWeek = $currentDate->modify('last Monday')->format('Y-m-d');
    $endOfLastWeek = $currentDate->modify('next Sunday')->setTime(23, 59)->format('Y-m-d H:i');

    $sql = "
        SELECT SUM(quantity) AS total_quantity
        FROM usage_reports
        WHERE name = ? AND day_counted >= ? AND day_counted <= ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $productName, $startOfLastWeek, $endOfLastWeek);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalOrdersLastWeek = $row['total_quantity'] ?? 0;

    return ($totalOrdersLastWeek > 0) ? round($totalOrdersLastWeek / 7) : 0;
}

function getUsersByType2($userType)
{
    global $conn;
    $sql = "SELECT email, name, uid FROM accounts WHERE userType = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userType);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();

    return $users;
}

// Function to check if the user has already been notified today
function hasBeenNotifiedToday2($conn, $uid)
{
    $currentDate = date('Y-m-d'); // Get current date
    $sql = "SELECT COUNT(*) AS count FROM notify_user WHERE uid = ? AND DATE(date_notified) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $uid, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Get all products
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$ingredientThresholds = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get product ingredients
        $ingredients = json_decode($row['ingredients'], true);

        // Calculate the average orders per day and low stock threshold for the product
        $averageOrdersPerDay = getAverageOrdersPerDay($conn, $row['name']);
        $lowStockThreshold = $averageOrdersPerDay * 3;

        foreach ($ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['ingredient_name']);
            $ingredientQuantity = floatval($ingredient['quantity']);
            $ingredientMeasurement = $ingredient['measurement'];

            // Convert grams to kg if necessary
            if ($ingredientMeasurement === 'grams') {
                $ingredientQuantity /= 1000; // Convert to kg
                $ingredientMeasurement = 'kg'; // Update measurement
            }

            // Calculate the total quantity needed for the ingredient based on the low stock threshold
            $totalQuantityNeeded = $ingredientQuantity * $lowStockThreshold;

            // Add the quantity needed to the ingredient thresholds array
            if (!isset($ingredientThresholds[$ingredientName])) {
                $ingredientThresholds[$ingredientName] = ['quantity' => 0, 'measurement' => $ingredientMeasurement];
            }
            $ingredientThresholds[$ingredientName]['quantity'] += $totalQuantityNeeded;
        }
    }
} else {
    echo '<p>No products found.</p>';
}

$stmt->close();

// Update inventory status based on thresholds
$lowStockIngredients = [];
$outOfStockIngredients = [];

foreach ($ingredientThresholds as $ingredientName => $data) {
    $threshold = $data['quantity'];
    $measurement = $data['measurement'];
    $updateInventoryStatusSql = "
        UPDATE daily_inventory
        SET status = CASE
            WHEN ending = 0 THEN 'out of stock'
            WHEN ending > 0 AND ending <= ? THEN 'low stock'
            WHEN ending > ? THEN 'in stock'
            ELSE status
        END
        WHERE LOWER(name) = ?
    ";

    $updateInventoryStatusStmt = $conn->prepare($updateInventoryStatusSql);
    $updateInventoryStatusStmt->bind_param("dds", $threshold, $threshold, $ingredientName);
    $updateInventoryStatusStmt->execute();
    $updateInventoryStatusStmt->close();

    // Fetch the updated status
    $fetchStatusSql = "SELECT ending, uom, status FROM daily_inventory WHERE LOWER(name) = ?";
    $fetchStatusStmt = $conn->prepare($fetchStatusSql);
    $fetchStatusStmt->bind_param("s", $ingredientName);
    $fetchStatusStmt->execute();
    $fetchStatusResult = $fetchStatusStmt->get_result();
    if ($fetchStatusResult->num_rows > 0) {
        $fetchStatusRow = $fetchStatusResult->fetch_assoc();
        $currentStock = $fetchStatusRow['ending'];
        $currentUom = $fetchStatusRow['uom'];
        $status = $fetchStatusRow['status'];

        if ($status === 'low stock') {
            $lowStockIngredients[] = [
                'ingredient' => ucfirst($ingredientName),
                'current_stock' => $currentStock . ' ' . $currentUom,
                'threshold' => $threshold . ' ' . $measurement
            ];
        } elseif ($status === 'out of stock') {
            $outOfStockIngredients[] = [
                'ingredient' => ucfirst($ingredientName),
                'threshold' => $threshold . ' ' . $measurement
            ];
        }
    }
    $fetchStatusStmt->close();
}

// Check if we have low stock or out of stock ingredients and send emails
if (count($lowStockIngredients) > 0 || count($outOfStockIngredients) > 0) {
    // Define user types
    $userTypes = ['admin', 'stockman', 'super_admin'];

    // Loop through each user type and send an email
    foreach ($userTypes as $userType) {
        $users = getUsersByType2($userType);

        foreach ($users as $user) {
            $email = $user['email'];
            $name = $user['name'];
            $uid = $user['uid'];

            // Check if this user has already been notified today
            if (!hasBeenNotifiedToday2($conn, $uid)) {
                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'phcr.inventory@gmail.com';
                    $mail->Password = 'qumsybajaxdmuyqv'; // Use a secure app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('phcr.inventory@gmail.com', 'PHCR Notifications');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Stock Notification';

                    $message = 'Dear ' . $name . ',<br><br>';
                    $message .= 'There are some stock issues:<br>';

                    if (count($lowStockIngredients) > 0) {
                        $message .= 'Low stock ingredients: ' . implode(', ', array_column($lowStockIngredients, 'ingredient')) . '<br>';
                    }

                    if (count($outOfStockIngredients) > 0) {
                        $message .= 'Out of stock ingredients: ' . implode(', ', array_column($outOfStockIngredients, 'ingredient')) . '<br>';
                    }
                    $message .= '<br>Kindly Check the inventory system for more details.<br> www.pizzahut-chinoroces.com<br>';
                    $message .= 'Please take appropriate action.<br><br>Best Regards,<br>PHCR Inventory System';

                    $mail->Body = $message;

                    $mail->send();
                    // echo 'Email sent to ' . $email . '<br>';

                    // Insert the user UID and today's date into the notify_user table
                    $insertSql = "INSERT INTO notify_user (uid) VALUES (?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("i", $uid);
                    $insertStmt->execute();
                    $insertStmt->close();
                } catch (Exception $e) {
                    // echo "Error sending email: " . $mail->ErrorInfo . '<br>';
                }
            } else {
                // echo 'User ' . $name . ' has already been notified today.<br>';
            }
        }
    }
} else {
    echo '<p>No ingredients with low stock or out of stock.</p>';
}

$conn->close();
?>


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
        max-height: 80vh;
        /* Limit the height of the modal */
        overflow-y: auto;
        /* Enable vertical scrolling if content overflows */
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

    /* List styling */
    .warning-modal-content ul {
        max-height: 300px;
        /* Limit the height of the list */
        overflow-y: auto;
        /* Enable vertical scrolling if content overflows */
        padding: 0;
        margin: 0;
        list-style-type: none;
    }

    .warning-modal-content li {
        margin-bottom: 10px;
    }
</style>

<!-- Modal HTML -->
<div class="warning-modal" id="warningModal">
    <div class="warning-modal-content">
        <span class="warning-icon">&#9888;</span> <!-- Warning Icon -->
        <div class="warning-message">
            <p style="font-size:1.5rem; font-weight:bold;" id="unavailable-message"></p>
            <div id="low-stock-section">
                <ul id="low-stock-list" style="text-align:left; margin-left:50px;"></ul> <!-- List for low stock ingredients -->
            </div>
            <div id="out-of-stock-section">
                <ul id="out-of-stock-list" style="text-align:left;margin-left:50px;"></ul> <!-- List for out of stock ingredients -->
            </div>
            <div class="warning-modal-buttons">
                <button class="btn-click-later" onclick="handleClickLater()">Remind Me Later</button>
                <button class="btn-reorder-now" onclick="handleReorderNow()">Reorder Now</button>
            </div>
        </div>
    </div>
</div>

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
        // Check if 'clickLater' is set to 'true' in session storage
        if (sessionStorage.getItem('clickLater') === 'true') {
            return; // Do not show the modal
        }

        const lowStockIngredients = <?php echo json_encode($lowStockIngredients); ?>;
        const outOfStockIngredients = <?php echo json_encode($outOfStockIngredients); ?>;

        // Show the low stock ingredients
        // Show the low stock ingredients
        const lowStockListElement = document.getElementById('low-stock-list');
        if (lowStockIngredients.length > 0) {
            lowStockIngredients.forEach(ingredient => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<strong>- ${ingredient.ingredient}</strong><br>(Current Stock: ${ingredient.current_stock}, <strong>Reorder: ${ingredient.threshold})</strong>`;
                lowStockListElement.appendChild(listItem);
            });
        } else {
            document.getElementById('low-stock-section').style.display = 'none';
        }

        // Show the out-of-stock ingredients
        const outOfStockListElement = document.getElementById('out-of-stock-list');
        if (outOfStockIngredients.length > 0) {
            outOfStockIngredients.forEach(ingredient => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<strong>- ${ingredient.ingredient}</strong><br>(Out of stock, <strong>Reorder: ${ingredient.threshold})</strong>`;
                outOfStockListElement.appendChild(listItem);
            });
        } else {
            document.getElementById('out-of-stock-section').style.display = 'none';
        }


        // Display the unavailable count message
        const unavailableCount = lowStockIngredients.length + outOfStockIngredients.length;
        const unavailableMessageElement = document.getElementById('unavailable-message');
        if (unavailableCount > 0) {
            if (lowStockIngredients.length > 0 && outOfStockIngredients.length > 0) {
                unavailableMessageElement.textContent = `${lowStockIngredients.length} low stock items and ${outOfStockIngredients.length} out of stock items`;
            } else if (lowStockIngredients.length > 0) {
                unavailableMessageElement.textContent = `${lowStockIngredients.length} low stock items`;
            } else if (outOfStockIngredients.length > 0) {
                unavailableMessageElement.textContent = `${outOfStockIngredients.length} out of stock items`;
            }
            showModal();
        } else {
            hideModal();
        }
    };

    // Button actions
    function handleReorderNow() {
        sessionStorage.setItem('clickLater', 'true');
        window.location.href = 'https://my305028.s4hana.ondemand.com/ui?sap-language=EN&help-mixedLanguages=false&help-autoStartTour=PR_A8DA8C2F83492685#PurchaseRequisition-process&/?sap-iapp-state--history=TASXGJYIADHA21QZX0GGYQ5LYKJSIV3T2N87KL25Z'; // Redirect to reorder page
        hideModal();
    }

    function handleClickLater() {
        sessionStorage.setItem('clickLater', 'true');
        hideModal();
    }
</script>