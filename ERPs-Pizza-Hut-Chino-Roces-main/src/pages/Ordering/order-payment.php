<?php
session_start();

include 'connection/database-conn.php';
include 'connection/database-db.php';

// Generate a new form token on every request
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

$formToken = $_SESSION['form_token']; // Store token in a variable to use in HTML

// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];

    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'users' with your table name
    $result = $conn->query($sql);
    $hasActiveOrders = false;
    $orderStatuses = ["placed", "preparing", "ready for pickup", "delivery"];
    //get the selected address from the session
    if (isset($_SESSION['selectedAddress'])) {
        $selectedAddress = $_SESSION['selectedAddress'];
    } else {
        $selectedAddress = "";
    }

    // Query the database to check for orders with specified statuses
    $checkOrdersSql = "SELECT COUNT(*) AS orderCount FROM orders WHERE uid = $currentUserId AND status IN ('" . implode("','", $orderStatuses) . "')";
    $resultOrders = $conn->query($checkOrdersSql);

    if ($resultOrders) {
        $rowOrders = $resultOrders->fetch_assoc();
        $hasActiveOrders = ($rowOrders['orderCount'] > 0);
    }
    // Retrieve the current user's ID from the session
    $sql = "SELECT status FROM orders WHERE uid = $currentUserId";
    $result = $conn->query($sql);

    // Check if there is a result
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_status = $row['status'];

            // Check if the order status is 'placed' or 'delivery'
            if ($order_status == 'placed' || $order_status == 'delivery' || $order_status == 'ready for pickup' || $order_status == 'preparing') {
                // Redirect to another page
                header("Location: order-placed.php");
                exit();
            }
        }
    }
    $isCartEmpty = true;
    $sqlCartCheck = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultCartCheck = $conn->query($sqlCartCheck);


    $sqlactiveOrder = "SELECT * FROM orders WHERE uid = $currentUserId AND status IN ('placed', 'ready for pickup', 'preparing', 'delivery')";
    $resultactiveOrder = $conn->query($sqlactiveOrder);
    $orderActive = true;

    if ($resultCartCheck->num_rows > 0) {
        $isCartEmpty = false;
    }
    if ($resultactiveOrder->num_rows > 0) {
        $orderActive = false;
    }
    if ($orderActive && $isCartEmpty) {
        header("Location: menu.php");
        exit();
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
} else {
    header("Location: ../../../login.php");
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../login.php");
    exit();
}

// Create connection

$queryz = "SELECT COUNT(*) as unread_count FROM msg_users WHERE status = 'unread' AND uid =" . $_SESSION['uid'];
$result41 = $db->query($queryz);

if ($result41) {
    $row41 = $result41->fetch_assoc();
    $unreadNotificationCount = $row41['unread_count'];
} else {
    $unreadNotificationCount = 0; // Default to 0 if query fails
}




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/pizzahut-logo.png">
    <title>Orders | Pizza Hut Chino Roces</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/orders.css">
    <script src="../../../src/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../src/bootstrap/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/search-index.js"></script>
</head>
<style>
    .pay-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        position: relative;
    }

    .pay-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 500px;
        text-align: center;
        padding: 20px 40px;
    }

    .pay-card h2 {
        margin-bottom: 30px;
        font-size: 24px;
        color: #333;
    }

    label {
        font-size: 14px;
        color: #555;
        margin-top: 10px;
        text-align: left;
        display: block;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    input:focus,
    select:focus {
        border-color: #4CAF50;
        outline: none;
    }

    .pay-submit-btn button {
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        order: 2;
    }

    .pay-submit-btn button:hover {
        background-color: #45a049;
    }

    .pay-error-message {
        color: red;
        margin-top: 10px;
    }

    .pay-success-message {
        color: green;
        margin-top: 10px;
        font-size: 0.9rem;
    }

    /* Card Type Selection */
    .pay-card-type-selector {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }


    .pay-card-icons {
        display: flex;
        margin-left: 10px;
        gap: 10px;
        margin-bottom: 10px;
    }

    .pay-card-icon {
        width: 50px;
        height: auto;
        opacity: 0.3;
        /* Hide images initially */
        pointer-events: none;
    }

    .pay-card-icon.visible {
        opacity: 1;
        /* Show the selected card image */
    }

    .btn-secondary {
        font-size: 1rem;
        text-align: center;
        justify-content: center;
        height: auto;
        display: flex;
        align-items: center;
        order: 1;
    }


    .pay-submit-btn {
        display: flex;
        justify-content: flex-end;
        gap: 20px;
    }
</style>

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
                    <a href="order.php" class="item active" id="orderLink">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Orders</span>
                    </a>
                    <a href="order-history.php" class="item">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Records</span>
                    </a>
                    <a href="messages.php" class="item-last" id="messagesLink">
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
                        <a href="order.php?logout=1" class="item">
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
            <div class="col-sm-11 wrap" style="padding:15px; height:100vh; ">
                <?php
                if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
                    echo '<div class="success" id="message-box">';
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    echo '</div>';
                }

                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="wrapper">
                            <h2><i class="fa-solid fa-utensils" style="margin-left:5px;"></i> My Orders</h2>
                            <div class="upper-buttons">
                                <a href="order-history.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-file-invoice"></i> Order History</a>
                                <a href="menu.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-bag-shopping"></i> My Bag</a>
                                <a href="messages.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-bell"></i> Messages</a>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-5" style="height:85vh; padding:20px 30px 20px 30px;">
                                    <div class="cart-summary" style="height:80vh; width:100%;">
                                        <div class="col-sm-12">
                                            <h3>Order Summary</h3>
                                            <hr>
                                        </div>
                                        <div class="col-sm-12 cart"
                                            style="margin:0 0 0 0; padding:0; height:35vh; overflow-y: scroll; overflow:auto; border-radius:25px; ">
                                            <?php
                                            if ($loggedIn) {
                                                $sql = "SELECT * FROM cart WHERE uid = $currentUserId";
                                                $result = $db->query($sql);
                                                $result1 = $db->query($sql);
                                                $newrow = mysqli_fetch_array($result1);
                                                if ($result->num_rows > 0) {
                                                    $cart = array();
                                                    // Display events
                                                    while ($row = $result->fetch_assoc()) {
                                                        $cart[] = $row;
                                                    }
                                                    $cart = array_reverse($cart);
                                                    foreach ($cart as $row) {
                                                        echo '
                                                            <div class="box" style="padding: 10px;border-radius:10px; margin: 10px 10px 10px 5px; position:relative; margin-left:10px;">
                                                                <div class="container" style="margin:0; padding:0;">
                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="image" style="height:100%; width:100%">
                                                                                <img src="../../../src/assets/img/menu/' . $row['img'] . '" alt="notif pic" style="width:100%; max-width:100%; min-width:100px; height:auto; overflow:hidden; border-radius:10px;">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="caption">
                                                                                <p>' . $row['size'] . ' ' . $row['name'] . '</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-2 bottom-footer">
                                                                            <div class="price">
                                                                                <p><span class="price-display" data-id="' . $row['cart_id'] . '">₱' . $row['price'] . '</span></p>
                                                                                <input type="hidden" class="price" name="price" data-id="' . $row['cart_id'] . '" value="' . $row['price'] . '">
                                                                                <div class="quantity1">
                                                                                    <select class="quantity" name="quantity" data-id="' . $row['cart_id'] . '" disabled>';
                                                        $sizes = explode(',', $row['qty']);

                                                        // Iterate over the 'size' data and create an option for each size
                                                        foreach ($sizes as $size) {
                                                            echo '<option value="' . $size . '">' . ucfirst($size) . '</option>';
                                                        }
                                                        echo '</select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            ';
                                                    }
                                                } else {
                                                    echo '<p style="text-align:center; margin-top:50px;">Add Items to your Bag</p> ';
                                                }
                                            } else {
                                                echo '<p style="text-align:center; margin-top:50px;">Please Login to Continue</p> ';
                                            }
                                            ?>
                                        </div>
                                        <div class="col-sm-12" style="margin: 30px 0 0 0;">
                                            <div class="linebreak" style="margin:0 15px 0 5px;">
                                                <hr style="height:2px;">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p style="font-weight:550">Vatable Sales</p>
                                                        <p style="font-weight:550">Vat (12%)</p>
                                                        <p style="font-weight:550">Subtotal</p>
                                                    </div>
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p id="vatable" style="margin-left: 30px; font-weight:bold;"></p>
                                                        <p id="vat" style="margin-left:30px; font-weight:bold;"></p>
                                                        <p id="sub_total" style="margin-left:30px; font-weight:bold;"></p>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="linebreak" style="margin:0 15px 0 5px;">
                                                <hr style="height:2px;">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p style="font-weight:550">Delivery Fee</p>
                                                        <p style="font-weight:550">Total Amount</p>
                                                    </div>
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p id="delivery_fee"
                                                            style="margin-left:30px; font-weight:bold;"></p>
                                                        <p id="total_amount"
                                                            style="margin-left:30px; font-weight:bold; color: #a12c12; font-size:1.3rem;">
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-7" style=" height:85vh; padding:20px 30px 20px 30px;">
                                    <div class="deliveryInfo" style="height:80vh; width:100%;">
                                        <div class="col-sm-12">
                                            <div class="pay-container">
                                                <div class="pay-card">
                                                    <form id="pay-form" action="submit-online.php" method="POST">
                                                        <h2>Payment Details</h2>
                                                        <label for="card_type">Card Type</label>
                                                        <div class="pay-card-type-selector">
                                                            <select id="card_type" name="card_type" required>
                                                                <option value="">Select Card Type</option>
                                                                <option value="visa">Visa</option>
                                                                <option value="mastercard">MasterCard</option>
                                                                <option value="amex">American Express</option>
                                                                <option value="discover">Discover</option>
                                                            </select>
                                                            <div class="pay-card-icons">
                                                                <img src="../../assets/img/visa.png" alt="Visa" id="visa" class="pay-card-icon" />
                                                                <img src="../../assets/img/card.png" alt="MasterCard" id="mastercard" class="pay-card-icon" />
                                                                <img src="../../assets/img/american-express.png" alt="American Express" id="amex" class="pay-card-icon" />
                                                                <img src="../../assets/img/discover.png" alt="Discover" id="discover" class="pay-card-icon" />
                                                            </div>
                                                        </div>
                                                        <label for="card_number">Card Number</label>
                                                        <input type="text" id="card_number" name="card_number" placeholder="Enter your card number" maxlength="19" required>



                                                        <label for="expiry_date">Expiry Date (MM/YY)</label>
                                                        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5" required>


                                                        <label for="security_code">Security Code (CVV)</label>
                                                        <input type="text" id="security_code" name="security_code" placeholder="Enter CVV" required maxlength="4">
                                                        <label for="name">Cardholder Name</label>
                                                        <input type="text" id="name" name="name" placeholder="Enter your name" required>

                                                        <div class="pay-submit-btn">
                                                            <button type="submit">Confirm</button>
                                                            <a href="order.php" class="btn btn-secondary">Cancel</a>
                                                        </div>

                                                        <div class="pay-error-message" id="error-message" style="display:none;"></div>
                                                        <div class="pay-success-message" id="success-message" style="display:none;"></div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- ENDING OF BODY -->
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <script>
        document.getElementById("pay-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form submission to handle validation

            // Clear any previous messages
            document.getElementById("error-message").style.display = "none";
            document.getElementById("success-message").style.display = "none";

            // Get form values
            const name = document.getElementById("name").value;
            const cardType = document.getElementById("card_type").value;
            const cardNumber = document.getElementById("card_number").value.replace(/\s+/g, '');
            const expiryDate = document.getElementById("expiry_date").value;
            const securityCode = document.getElementById("security_code").value;

            // Simple validation
            if (!name || !cardType || !cardNumber || !expiryDate || !securityCode) {
                showError("All fields are required.");
                return;
            }

            if (!/^\d{13,16}$/.test(cardNumber)) {
                showError("Invalid Card Number");
                return;
            }



            // Validate expiry date (MM/YY)
            const expiryParts = expiryDate.split("/");
            if (expiryParts.length !== 2 || !/^\d{2}$/.test(expiryParts[0]) || !/^\d{2}$/.test(expiryParts[1])) {
                showError("Invalid expiry date. Use MM/YY format.");
                return;
            }

            //invalid month on expiry date 
            if (parseInt(expiryParts[0]) > 12 || parseInt(expiryParts[0]) < 1) {
                showError("Invalid Card");
                return;
            }

            //check if the expiry date is valid
            const currentYear = new Date().getFullYear().toString().slice(-2);
            const currentMonth = new Date().getMonth() + 1;
            const expiryYear = parseInt(expiryParts[1], 10);
            const expiryMonth = parseInt(expiryParts[0], 10);
            if (expiryYear < currentYear || (expiryYear === currentYear && expiryMonth < currentMonth)) {
                showError("Card has expired. Please use a valid card.");
                return;
            }
            showSuccess("Payment details are confirmed. Processing order...");

            setTimeout(function() {
                document.getElementById("pay-form").submit();
            }, 2000);


        });

        function showError(message) {
            const errorMessage = document.getElementById("error-message");
            errorMessage.textContent = message;
            errorMessage.style.display = "block";
        }

        function showSuccess(message) {
            const successMessage = document.getElementById("success-message");
            successMessage.textContent = message;
            successMessage.style.display = "block"; // Show success message if needed
        }


        // Show the correct card image based on selection
        document.getElementById("card_type").addEventListener("change", function() {
            const selectedCard = this.value;
            const cardImages = document.querySelectorAll('.pay-card-icon');

            // Hide all card images
            cardImages.forEach(img => img.classList.remove('visible'));

            // Show the selected card type image
            if (selectedCard) {
                document.getElementById(selectedCard).classList.add('visible');
            }



        });
        document.getElementById("expiry_date").addEventListener("input", function(event) {
            let input = event.target.value.replace(/\D/g, ""); // Remove all non-numeric characters
            if (input.length >= 2) {
                input = input.substring(0, 2) + "/" + input.substring(2); // Add slash after the second digit
            }
            if (input.length > 5) {
                input = input.substring(0, 5); // Limit input to 5 characters (MM/YY)
            }
            event.target.value = input; // Update the input field value
        });
        document.getElementById("card_number").addEventListener("input", function(event) {
            let input = event.target.value.replace(/\D/g, ""); // Remove all non-numeric characters
            input = input.match(/.{1,4}/g)?.join(" ") || input; // Group digits in sets of 4 and join with space
            event.target.value = input; // Update the input field value
        });

        document.getElementById("name").addEventListener("input", function(event) {
            let input = event.target.value.replace(/\d/g, ""); // Remove all numeric characters
            event.target.value = input; // Update the input field value
        });

        //dont let the user input letters on security code 
        document.getElementById("security_code").addEventListener("input", function(event) {
            let input = event.target.value.replace(/\D/g, ""); // Remove all non-numeric characters
            event.target.value = input; // Update the input field value
        });
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