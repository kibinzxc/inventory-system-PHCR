<?php
include '../../connection/database.php';
error_reporting(1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../phpmailer/src/Exception.php';
require '../../../phpmailer/src/PHPMailer.php';
require '../../../phpmailer/src/SMTP.php';

// Check if the current time is between 1:00 AM and 4:00 AM
$currentTime = date('H:i');
if ($currentTime >= '01:00' && $currentTime <= '23:59') {
    // Get yesterday's date
    $yesterdayDate = date('Y-m-d', strtotime('yesterday'));

    // Query the records_inventory table to check if there's a record for yesterday
    $inventoryCheckSql = "SELECT COUNT(*) AS count FROM records_inventory WHERE inventory_date = ?";
    $inventoryCheckStmt = $conn->prepare($inventoryCheckSql);
    $inventoryCheckStmt->bind_param("s", $yesterdayDate);
    $inventoryCheckStmt->execute();
    $inventoryCheckResult = $inventoryCheckStmt->get_result();
    $inventoryCheckRow = $inventoryCheckResult->fetch_assoc();

    if ($inventoryCheckRow['count'] == 0) {
        // No record for yesterday, check if the super admin has already been notified today
        $currentDate = date('Y-m-d');
        $notificationCheckSql = "SELECT COUNT(*) AS count FROM forgot_user WHERE DATE(date_notified)=?";
        $notificationCheckStmt = $conn->prepare($notificationCheckSql);
        $notificationCheckStmt->bind_param("s", $currentDate);
        $notificationCheckStmt->execute();
        $notificationCheckResult = $notificationCheckStmt->get_result();
        $notificationCheckRow = $notificationCheckResult->fetch_assoc();

        if ($notificationCheckRow['count'] == 0) {
            // Get super admin's details
            $adminSql = "SELECT email, name, uid FROM accounts WHERE userType = 'super_admin' LIMIT 1";
            $adminStmt = $conn->prepare($adminSql);
            $adminStmt->execute();
            $adminResult = $adminStmt->get_result();

            if ($adminResult->num_rows > 0) {
                $adminRow = $adminResult->fetch_assoc();
                $superAdminEmail = $adminRow['email'];
                $superAdminName = $adminRow['name'];
                $superAdminUid = $adminRow['uid'];

                // Send email to the super admin
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
                    $mail->addAddress($superAdminEmail);

                    $mail->isHTML(true);
                    $mail->Subject = 'Missing End of Day Inventory Submission';

                    $message = 'Dear ' . $superAdminName . ', <br><br>';
                    $message .= 'It appears that the end-of-day inventory record for ' . $yesterdayDate . ' is missing. Please remind the employees to submit the inventory for the day.<br>';
                    $message .= 'Thank you for your attention to this matter.<br><br>';
                    $message .= 'Best regards,<br>PHCR Inventory System';

                    $mail->Body = $message;

                    $mail->send();
                    // echo 'Reminder email sent to ' . $superAdminEmail . '<br>';

                    // Insert the notification record into the forgot_user table
                    $insertNotificationSql = "INSERT INTO forgot_user (uid) VALUES (?)";
                    $insertNotificationStmt = $conn->prepare($insertNotificationSql);
                    $insertNotificationStmt->bind_param("i", $superAdminUid);
                    $insertNotificationStmt->execute();
                    $insertNotificationStmt->close();
                } catch (Exception $e) {
                    // echo "Error sending email: " . $mail->ErrorInfo . '<br>';
                }
            }
        }
    }
}

// Check if it's 11:00 PM
$currentDate = date('Y-m-d');
if ($currentTime == '23:00') {
    // Check if today's records exist in the `records_inventory` table
    $inventoryCheckSql = "SELECT COUNT(*) AS count FROM records_inventory WHERE DATE(inventory_date) = ?";
    $inventoryCheckStmt = $conn->prepare($inventoryCheckSql);
    $inventoryCheckStmt->bind_param("s", $currentDate);
    $inventoryCheckStmt->execute();
    $inventoryCheckResult = $inventoryCheckStmt->get_result();
    $inventoryCheckRow = $inventoryCheckResult->fetch_assoc();

    if ($inventoryCheckRow['count'] == 0) {
        // No records for today, send reminder email

        // Get users who need to be reminded
        $userTypes = ['admin', 'stockman', 'super_admin'];

        foreach ($userTypes as $userType) {
            $users = getUsersByType($userType);

            foreach ($users as $user) {
                $email = $user['email'];
                $name = $user['name'];
                $uid = $user['uid'];

                // Check if the user has already been notified today
                if (!hasBeenNotifiedToday($conn, $uid, 'remind_user')) {
                    // Send reminder email
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
                        $mail->Subject = 'Reminder: End of Day Inventory Submission';

                        $message = 'Dear ' . $name . ', <br><br>';
                        $message .= 'This is a reminder to submit your end-of-day inventory. It seems you haven\'t submitted it yet for today.<br>';
                        $message .= 'Please make sure to update the inventory before the day ends.<br>';
                        $message .= 'Thank you for your cooperation.<br><br>';
                        $message .= 'Best regards,<br>PHCR Inventory System';

                        $mail->Body = $message;

                        $mail->send();
                        // echo 'Reminder email sent to ' . $email . '<br>';

                        // Insert the user UID and today's date into the remind_user table
                        $insertReminderSql = "INSERT INTO remind_user (uid, date_reminded) VALUES (?, NOW())";
                        $insertReminderStmt = $conn->prepare($insertReminderSql);
                        $insertReminderStmt->bind_param("i", $uid);
                        $insertReminderStmt->execute();
                        $insertReminderStmt->close();
                    } catch (Exception $e) {
                        // echo "Error sending email: " . $mail->ErrorInfo . '<br>';
                    }
                } else {
                    // echo 'User ' . $name . ' has already been reminded today.<br>';
                }
            }
        }
    }
}

// Get all products and check ingredient availability
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $allIngredientsAvailable = true;

        // Get product ingredients
        $ingredients = json_decode($row['ingredients'], true);

        foreach ($ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['ingredient_name']);
            $ingredientQuantity = floatval($ingredient['quantity']);
            $ingredientMeasurement = $ingredient['measurement'];

            // Check ingredient availability in the inventory
            $inventorySql = "SELECT * FROM daily_inventory WHERE name = ?";
            $inventoryStmt = $conn->prepare($inventorySql);
            $inventoryStmt->bind_param("s", $ingredientName);
            $inventoryStmt->execute();
            $inventoryResult = $inventoryStmt->get_result();
            $isIngredientAvailable = false;

            if ($inventoryResult->num_rows > 0) {
                $inventoryRow = $inventoryResult->fetch_assoc();
                $inventoryMeasurement = $inventoryRow['uom'];
                $availableStock = floatval($inventoryRow['ending']);
                $availableStockInKg = 0;
                $availableStockInPieces = 0;
                $availableStockInBottle = 0;

                // Convert available stock based on measurement unit
                if ($inventoryMeasurement == 'grams') {
                    $availableStockInKg = $availableStock / 1000;  // Convert grams to kg
                } elseif ($inventoryMeasurement == 'kg') {
                    $availableStockInKg = $availableStock;
                } elseif ($inventoryMeasurement == 'pc') {
                    $availableStockInPieces = $availableStock;
                } elseif ($inventoryMeasurement == 'bt') {
                    $availableStockInBottle = $availableStock;
                }

                // Check availability based on ingredient's measurement
                if ($ingredientMeasurement == 'pcs' && $availableStockInPieces >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement == 'grams' && $availableStockInKg >= ($ingredientQuantity / 1000)) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement == 'bottle' && $availableStockInBottle >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement == 'pc' && $availableStockInPieces >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                }
            }

            // If any ingredient is not available, set the flag to false
            if (!$isIngredientAvailable) {
                $allIngredientsAvailable = false;
                break;
            }
        }

        // Update product status based on ingredient availability
        $updateStatusSql = $allIngredientsAvailable ?
            "UPDATE products SET status = 'available' WHERE prodID = ?" :
            "UPDATE products SET status = 'not available' WHERE prodID = ?";
        $updateStatusStmt = $conn->prepare($updateStatusSql);
        $updateStatusStmt->bind_param("i", $row['prodID']);
        $updateStatusStmt->execute();
        $updateStatusStmt->close();
    }
} else {
    // echo '<p>No products found.</p>';
}

$stmt->close();
$conn->close();

// Function to get users by type
function getUsersByType($userType)
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
function hasBeenNotifiedToday($conn, $uid, $table)
{
    $currentDate = date('Y-m-d'); // Get current date
    $sql = "SELECT COUNT(*) AS count FROM $table WHERE uid = ? AND DATE(date_reminded) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $uid, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}
