<?php
session_start();
include 'connection/database-conn.php';
include 'connection/database-db.php';


// Create connection
// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];


    // Create connection

    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'users' with your table name
    $result = $conn->query($sql);
    $hasActiveOrders = false;
    $orderStatuses = ["placed", "preparing", "delivery"];
    // Query the database to check for orders with specified statuses
    $checkOrdersSql = "SELECT COUNT(*) AS orderCount FROM orders WHERE uid = $currentUserId AND status IN ('" . implode("','", $orderStatuses) . "')";
    $resultOrders = $conn->query($checkOrdersSql);

    if ($resultOrders) {
        $rowOrders = $resultOrders->fetch_assoc();
        $hasActiveOrders = ($rowOrders['orderCount'] > 0);
    }

    // Retrieve the current user's ID from the session
    $currentUserId = $_SESSION['uid'];

    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'users' with your table name
    $result = $conn->query($sql);
    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'users' with your table name
    $result = $conn->query($sql);
    $hasActiveOrders = false;
    $orderStatuses = ["placed", "preparing", "delivery"];
    // Query the database to check for orders with specified statuses
    $checkOrdersSql = "SELECT COUNT(*) AS orderCount FROM orders WHERE uid = $currentUserId AND status IN ('" . implode("','", $orderStatuses) . "')";
    $resultOrders = $conn->query($checkOrdersSql);

    if ($resultOrders) {
        $rowOrders = $resultOrders->fetch_assoc();
        $hasActiveOrders = ($rowOrders['orderCount'] > 0);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userAddress = $row['address']; // Store the user's address in a variable
        $currentUserId = $currentUserId;
    } else {
        $userAddress = "House No, Street, City, Province"; // Set a default value if no address is found
    }
    $userTypeQuery = "SELECT user_type FROM users WHERE uid = $currentUserId";
    $result = $conn->query($userTypeQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userType = $row['user_type'];

        // Check if user_type is "customer"
        if ($userType !== "customer") {
            header("Location: ../../../login.php");
            exit(); // Ensure script stops execution after redirection
        }
    }
    $conn->close();
} else {
    header("Location: menu.php");
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../login.php");
    exit();
}


$queryz = "SELECT COUNT(*) as unread_count FROM msg_users WHERE status = 'unread' AND uid =" . $_SESSION['uid'];
$result41 = $db->query($queryz);

if ($result41) {
    $row41 = $result41->fetch_assoc();
    $unreadNotificationCount = $row41['unread_count'];
} else {
    $unreadNotificationCount = 0; // Default to 0 if query fails
}


function convertDateTimeFormat($dateTime)
{
    // Convert date to 'F d, Y' format
    $date = date("F d, Y", strtotime($dateTime));

    // Convert time to 12-hour format with AM and PM
    $time = date("h:i A", strtotime($dateTime));

    // Combine date and time
    $convertedDateTime = $date . ' ' . $time;

    return $convertedDateTime;
}

if (isset($_GET['delete'])) {
    $notificationId = $_GET['delete'];
    $deleteQuery = "DELETE FROM msg_users WHERE user_msgID = $notificationId AND uid=" . $_SESSION['uid'];
    if ($db->query($deleteQuery) === TRUE) {
        $_SESSION['success2']  = "Message has been successfully deleted";
        header("Location:archives.php ");
        exit();
    } else {
    }
}

if (isset($_GET['inbox'])) {
    $notificationId = $_GET['inbox'];
    $updateQuery = "UPDATE msg_users SET status = 'read' WHERE user_msgID = $notificationId AND uid =" . $_SESSION['uid'];
    if ($db->query($updateQuery) === TRUE) {
        $_SESSION['success2']  = "Message has been successfully restored";
        header("Location:archives.php ");
        exit();
    } else {
    }
}
if (isset($_POST['mark_all_read'])) {
    $deleteQuery = "DELETE FROM msg_users WHERE uid =" . $_SESSION['uid'] . " AND status = 'archived'";

    if ($db->query($deleteQuery) === TRUE) {
        $_SESSION['success2']  = "Message has been successfully deleted";
    } else {
        // Handle deletion error
        echo "Error deleting record: " . $db->error;
    }
}
$queryz1 = "SELECT COUNT(*) as archived_count FROM msg_users WHERE status = 'archived' AND uid =" . $_SESSION['uid'];
$result42 = $db->query($queryz1);

if ($result42) {
    $row42 = $result42->fetch_assoc();
    $unreadArchivedCount = $row42['archived_count'];
} else {
    $unreadArchivedCount  = 0; // Default to 0 if query fails
}
$isCartEmpty = true;

if ($loggedIn) {
    $sqlCartCheck = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultCartCheck = $db->query($sqlCartCheck);

    if ($resultCartCheck->num_rows > 0) {
        $isCartEmpty = false;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/pizzahut-logo.png">
    <title>Archives | Pizza Hut Chino Roces</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/messages.css">
    <script src="../../../src/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../src/bootstrap/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/search-index.js"></script>
</head>

<body>

    <div class="container-fluid" style="overflow:hidden;">
        <div class="row row-flex">
            <!-- Add the row-flex class -->
            <div class="col-sm-1 custom-width" style="height:100vh;">
                <!-- Add the custom-width class -->
                <div class="sidebar" style="height:100vh;">
                    <a href="../../../index.php" class="item1">
                        <img class="logo" src="../../assets/img/pizzahut-logo.png" alt="Pizza Hut Logo">
                    </a>
                    <a href="menu.php" class="item">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Menu</span>
                    </a>
                    <a href="order.php" class="item" id="orderLink">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Orders</span>
                    </a>
                    <a href="order-history.php" class="item">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Records</span>
                    </a>
                    <a href="messages.php" class="item-last active" id="messagesLink">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Messages</span>
                        <?php

                        $unreadNotificationCount = $unreadNotificationCount;

                        if ($unreadNotificationCount > 0) {
                            echo '<span class="notification-count">' . $unreadNotificationCount . '</span>';
                        }
                        ?>
                    </a>
                    <!-- Toggle Login/Logout link -->
                    <?php if ($loggedIn) : ?>
                        <a href="profile.php" class="item">
                            <i class="fa-solid fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <a href="view.php?logout=1" class="item">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    <?php else : ?><br><br>
                        <a href="../../../login.php" class="item-login">
                            <i class="fa-solid fa-user"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>

                </div>
            </div>
            <!-- BEGINNING OF BODY -->
            <div class="col-sm-11" style="background: white;">
                <div class="row">
                    <?php
                    if (isset($_SESSION['success1']) && !empty($_SESSION['success1'])) {
                        echo '<div class="success" id="message-box">';
                        echo $_SESSION['success1'];
                        unset($_SESSION['success1']);
                        echo '</div>';
                    }
                    if (isset($_SESSION['error1']) && !empty($_SESSION['error1'])) {
                        echo '<div class="error" id="message-box">';
                        echo $_SESSION['error1'];
                        unset($_SESSION['error1']);
                        echo '</div>';
                    }
                    ?>
                    <div class="col-md-5" style="height:100vh; border-right:2px solid #B6B6B6; overflow: auto;">
                        <div class="notifs" style=" margin: 0 20px 0 10px">
                            <h3 style="font-weight:700; margin-top:40px;">Archived Messages</h3>
                            <?php
                            if ($unreadArchivedCount > 0) {
                                echo '<form style="float:right; margin-top:-40px; margin-right:50px;" method="post">';
                                echo '<button type="submit" name="mark_all_read" class="read-all-button" style="border:none; text-decoration:none; background-color:white; color:#D24545;">Delete All</button>';
                                echo '</form>';
                            }
                            ?>
                            <a href="messages.php" class="archive1" title="Messages"><i
                                    class="fa-solid fa-envelope-open"
                                    style="float:right; margin-top:-40px; margin-right:10px; font-size:30px;"></i></a>
                            <hr>
                            <?php

                            $sql = "SELECT * FROM msg_users WHERE uid=" . $_SESSION['uid'] . " AND status = 'archived'";
                            $result = $db->query($sql);
                            $result1 = $db->query($sql);
                            $newrow = mysqli_fetch_array($result1);


                            // Check if any notifications were found
                            if ($result->num_rows > 0) {
                                $notifications = array();
                                // Display notifications
                                while ($row = $result->fetch_assoc()) {
                                    $notifications[] = $row;
                                }
                                $notifications = array_reverse($notifications);

                                foreach ($notifications as $row) {

                                    $category = $row['category'];
                                    $iconMapping = [
                                        "Order status" => "fa-solid fa-utensils",
                                        "Promotion" => "fa-solid fa-bullhorn",

                                    ];
                                    $defaultIcon = "bi bi-question"; // Adjust the default icon class name

                                    $icon = isset($iconMapping[$category]) ? $iconMapping[$category] : $defaultIcon;
                                    // Define an array that maps categories to their corresponding icons


                                    $dateString = $row['date_created']; // Your input date string
                                    $inputFormat = "Y-m-d"; // Format of the input date string
                                    $outputFormat = "F d, Y"; // Desired output format
                                    $timestamp = strtotime($dateString);

                                    // Format the timestamp into the desired output format
                                    $outputDate = date($outputFormat, $timestamp);
                                    $dateTime = $row['date_created'];
                                    $convertedDateTime = convertDateTimeFormat($dateTime);

                                    echo '<a class="notif" style="text-decoration:none; color:black;" href="view-archived.php?id=' . $row['user_msgID'] . '">
                            <div class="' . $row['status'] . '" style = "padding:20px 20px 5px 20px; width:100%; border-bottom:1px solid #B6B6B6; border-radius:5px; margin-bottom:10px;">
                                <div style = "float:left; margin-top:10px;"> 
                                    
                                    <img src="../../assets/img/bell.svg" alt="Category Icon" style="width:50px; color:#605D5D;"></img>
                                </div>
                                <div style = "float:center; margin-left:60px;">
                                    <h5>' . $row['title'] . '</h5>
                                    <p style="overflow:hidden; white-space: nowrap; text-overflow: ellipsis;">' . $row['category'] . ': ' . $row['description'] . '</p>
                                    <p style = "margin-top:-15px;">' . $outputDate . '</p>
                                    </div>
                            </div>
                            </a>';
                                }
                            } else {
                                echo '<h4 style="text-align:center; margin-top:320px;">No Notifications</h4>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div style="margin:40px 20px 0 20px;">
                            <?php
                            if (isset($_GET['id'])) {

                                $notif_id = $_GET['id'];

                                $sqly = "SELECT * FROM msg_users WHERE user_msgID = '$notif_id'";
                                $resultz = $db->query($sqly);




                                if ($resultz->num_rows > 0) {
                                    $rowz = $resultz->fetch_assoc();


                                    $dateTime = $rowz['date_created'];
                                    $convertedDateTime = convertDateTimeFormat($dateTime);
                                    echo '<p>' . $convertedDateTime . '</p> 
                        <h3>' . $rowz['title'] . '</h3><a class="button1" href="' . $_SERVER['PHP_SELF'] . '?inbox=' . $rowz['user_msgID'] . '" style="position:absolute; color:#a12c12; top:80px; right:100px;"><i class="fa-solid fa-inbox" style="font-size:30px;" title="Move to inbox"></i></a>
                        <a class="button1" href="' . $_SERVER['PHP_SELF'] . '?delete=' . $rowz['user_msgID'] . '" style="position:absolute; color:#a12c12; top:80px; right:50px;"><i class="fa-solid fa-trash-can" style="font-size:30px;" title="Delete"></i></a>
<hr>
                        <div class="middle" style="padding: 20px 50px 20px 50px; text-align:center; overflow:auto; height:760px;">
                        <img src="../../assets/img/' . $rowz['image'] . '" alt="notif pic" style="width:500px; max-width:100%; min-width:100px;">
                        <h5 style="margin-top:20px;">' . $rowz['category'] . '</h5>
                        <p style="font-family:verdana; font-size:15px; margin-top:20px; line-height: 1.8; text-align:justify;">' . $rowz['description'] . '</p>
                        <div>';
                                } else {
                                    echo '<h4 style="text-align:center; margin-top:400px;">No Message Selected</h4>';
                                }
                            } else {
                                echo '<h4 style="text-align:center; margin-top:400px;">Invalid Message ID!</h4>';
                            }

                            ?>


                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ENDING OF BODY -->

        <script>
            <?php if (!$loggedIn) : ?>
                document.getElementById('messagesLink').classList.add('disabled');
                document.getElementById('orderLink').classList.add('disabled');
            <?php endif; ?>
        </script>

        <script>
            <?php if (!$loggedIn) : ?>
                document.getElementById('messagesLink').classList.add('disabled');
                document.getElementById('orderLink').classList.add('disabled');
            <?php endif; ?>
        </script>

        <script>
            setTimeout(function() {
                var messageBox = document.getElementById('message-box');
                if (messageBox) {
                    messageBox.style.display = 'none';
                }
            }, 2000);
        </script>
        <script>
            <?php if ($isCartEmpty && !$hasActiveOrders) : ?>
                document.getElementById('orderLink').classList.add('disabled');
            <?php endif; ?>
        </script>
</body>

</html>