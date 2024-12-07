<?php
session_start();
include 'connection/database-conn.php';
include 'connection/database-db.php';
// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];
    // Database connection details

    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'customerInfo' with your table name
    $result = $conn->query($sql);
    $hasActiveOrders = false;
    $orderStatuses = ["placed", "preparing", "ready for pickup", "delivery"];

    // Query the database to check for orders with specified statuses
    $checkOrdersSql = "SELECT COUNT(*) AS orderCount FROM orders WHERE uid = $currentUserId AND status IN ('" . implode("','", $orderStatuses) . "')";
    $resultOrders = $conn->query($checkOrdersSql);

    if ($resultOrders) {
        $rowOrders = $resultOrders->fetch_assoc();
        $hasActiveOrders = ($rowOrders['orderCount'] > 0);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userAddressJson = $row['address']; // Assuming address is stored as JSON in the database
        $addresses = json_decode($userAddressJson, true); // Decode JSON into an associative array

        if (json_last_error() === JSON_ERROR_NONE) {
            // You can now access the addresses as an array
            // Example: Let's say you want to get all addresses and display them in a formatted way
            $formattedAddresses = [];
            foreach ($addresses as $address) {
                $formattedAddresses[] = $address['name'] . " - " . $address['address'];
            }
            // Join the formatted addresses into a single string
            $formattedAddress = implode(", ", $formattedAddresses);
        } else {
            // Default address if JSON is malformed
            $formattedAddress = "House No, Street, City, Province";
        }
    } else {
        $formattedAddress = "House No, Street, City, Province"; // Set a default value if no address is found
    }

    $queryz = "SELECT COUNT(*) as unread_count FROM msg_users WHERE status = 'unread' AND uid =" . $_SESSION['uid'];
    $result41 = $conn->query($queryz);

    if ($result41) {
        $row41 = $result41->fetch_assoc();
        $unreadNotificationCount = $row41['unread_count'];
    } else {
        $unreadNotificationCount = 0; // Default to 0 if query fails
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
    $loggedIn = false;
    $currentUserId = 123; // or any default value
    $userAddress = "";
    $unreadNotificationCount = 0;

    $hasActiveOrders = false; // Non-logged-in users won't have active orders
}

//check if there is an active order, if there is not the unset the session form_submitted 
if (!$hasActiveOrders) {
    unset($_SESSION['form_submitted']);
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../login.php");
    exit();
}

if (isset($_POST['view-order'])) {
    header("Location: order.php");
}
if (isset($_POST['addtobag'])) {
    if (!isset($_SESSION['uid'])) {
        header("Location: ../../../login.php");
        exit();
    }

    $uid = $_SESSION['uid'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $img = $_POST['img'];
    $size1 = $_POST['size'];
    //put a quotation mark on the size
    $size = '' . $size1 . '';
    $dish_id = $_POST['dish_id'];
    $quantity = 1;  // default quantity

    // Check if the dish_id already exists in the cart
    $check_sql = "SELECT * FROM cart WHERE dish_id = '$dish_id' AND uid = '$uid'";
    $result = mysqli_query($db, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $currentQuantity = $row['qty'];

        // Check if the current quantity plus the new quantity exceeds the maximum
        if (($currentQuantity + $quantity) > 10) {
            // Display an error message for reaching the maximum quantity
            $_SESSION['error'] = "You cannot add more than 10 items";
        } else {
            // Update the quantity and multiply the price
            $update_sql = "UPDATE cart SET qty = qty + $quantity, totalprice = totalprice + ($quantity * $price) WHERE dish_id = '$dish_id' AND uid = '$uid'";
            mysqli_query($db, $update_sql);
            $_SESSION['success']  = "Successfully added into your bag";
        }
    } else {
        // If the dish_id doesn't exist, insert a new row with the multiplied price
        $total_price = $quantity * $price;
        $insert_sql = "INSERT INTO cart (dish_id, uid, name, size, qty, price, img,totalprice) 
                        VALUES ('$dish_id', '$uid', '$name', '$size', '$quantity', '$price', '$img','$total_price')";
        mysqli_query($db, $insert_sql);
        $_SESSION['success']  = "Successfully added into your bag";
    }
}


function getCoordinates($address, $apiKey)
{
    $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($address) . "&key=" . $apiKey;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status']['code'] == 200 && !empty($data['results'])) {
        $lat = $data['results'][0]['geometry']['lat'];
        $lng = $data['results'][0]['geometry']['lng'];
        return ['lat' => $lat, 'lng' => $lng];
    }

    return null;
}

function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // in kilometers

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c;

    return $distance; // distance in kilometers
}
if (isset($_POST['checkout'])) {
    // Check if the form has already been submitted
    if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] === true) {
        $_SESSION['error'] = "Form already submitted. Please wait.";
        header("Location: menu.php"); // Redirect to prevent re-submission
        exit;
    }

    // Mark the form as submitted
    $_SESSION['form_submitted'] = true;

    $selectedAddressId = $_POST['address'] ?? null;

    if ($selectedAddressId) {
        $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userAddressJson = $row['address'];
            $addresses = json_decode($userAddressJson, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $selectedAddress = null;
                foreach ($addresses as $address) {
                    if ((int)$address['address'] === (int)$selectedAddressId) {
                        $selectedAddress = $address;
                        break;
                    }
                }

                if ($selectedAddress) {
                    $apiKey = '9f368e104b744ddab127fb3cbcf84673';
                    $fixedAddress = '2116 Chino Roces Ave, Cor Dela Rosa Street, Pio, Makati, Metro Manila, Philippines';
                    $selectedAddress2 = $selectedAddress['address'] . ', Philippines';

                    $coordsSelected = getCoordinates($selectedAddress2, $apiKey);
                    $coordsFixed = getCoordinates($fixedAddress, $apiKey);

                    if ($coordsSelected && $coordsFixed) {
                        $distance = haversineDistance(
                            $coordsSelected['lat'],
                            $coordsSelected['lng'],
                            $coordsFixed['lat'],
                            $coordsFixed['lng']
                        );

                        if ($distance > 5) {
                            $_SESSION['error'] = "The delivery address is outside the maximum delivery range.";
                        } else {
                            $_SESSION['selectedAddress'] = $selectedAddress2;
                            // Clear form submission flag for future use
                            unset($_SESSION['form_submitted']);
                            header("Location: order.php");
                            exit;
                        }
                    } else {
                        $_SESSION['error'] = "Could not retrieve coordinates for one or both addresses.";
                    }
                } else {
                    $_SESSION['error'] = "Address not found for the selected ID.";
                }
            } else {
                $_SESSION['error'] = "Error decoding address JSON.";
            }
        } else {
            $_SESSION['error'] = "No address found for the user.";
        }
    } else {
        $_SESSION['error'] = "No address selected.";
    }

    // Clear the submission flag if an error occurs
    unset($_SESSION['form_submitted']);
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/pizzahut-logo.png">
    <title>Menu | Pizza Hut Chino Roces</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/menu.css">
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
                    <a href="menu.php" class="item active">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Menu</span>
                    </a>
                    <a href="order.php" class="item <?php echo ($hasActiveOrders) ? '' : 'disabled'; ?>" id="orderLink">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Orders</span>
                    </a>
                    <a href="order-history.php" class="item <?php echo ($loggedIn) ? '' : 'disabled'; ?>">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Records</span>
                    </a>
                    <a href="messages.php" class="item-last" id="messagesLink">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Messages</span>
                        <?php
                        // Include your PHP logic here to determine the count of unread notifications
                        $unreadNotificationCount = $unreadNotificationCount; // Replace with your actual logic

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
                        <a href="menu.php?logout=1" class="item">
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
            <div class="col-sm-9" style="background: white;">
                <div class="container">
                    <div class="row">
                        <?php
                        if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
                            echo '<div class="success" id="message-box">';
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            echo '</div>';
                        }
                        if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                            echo '<div class="error" id="message-box">';
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            echo '</div>';
                        }
                        ?>
                        <div class="col-sm-12"
                            style="padding:0; height:100%; overflow:hidden; border-radius:5px!important; margin-top:40px; width:100%;">
                            <img class="banner" src="../../assets/img/ph_banner2.png" alt="Banner"
                                style="width:100%; max-width:100%; min-width:100px; height:auto; overflow:hidden;">
                        </div>
                        <div class="col-sm-12" style="padding:0; margin:0; margin-top:30px;">
                            <div class="container" style="padding:0;">
                                <div class="row" style="padding:0px 15px 0 12px;">
                                    <div class="col-sm-4"
                                        style="text-align:center; border-bottom:5px solid red; margin-bottom:-20px;padding-bottom:20px;">
                                        <a href="menu.php" class="menu-item active">
                                            <span>Pizza</span>
                                        </a>
                                    </div>
                                    <div class="col-sm-4" style="text-align:center;">
                                        <a href="menu-pasta.php" class="menu-item">
                                            <span>Pasta</span>
                                        </a>
                                    </div>
                                    <div class="col-sm-4" style="text-align:center;">
                                        <a href="menu-beverages.php" class="menu-item">
                                            <span>Beverages</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="col-sm-12 scroll" style="overflow-y: auto; height: 50vh; margin:0; padding:0;">

                            <div class="flex-container">

                                <?php
                                $sql = "SELECT * FROM products where category = 'pizza' AND status = 'available' ORDER BY name asc ";
                                $result = $db->query($sql);
                                $result1 = $db->query($sql);
                                $newrow = mysqli_fetch_array($result1);


                                if ($result->num_rows > 0) {
                                    $appointment = array();


                                    while ($row = $result->fetch_assoc()) {

                                        echo '
                <form method="post" action="">
                <div class="flex-item" style="border-radius:5px;">
                    <div class="head-card" style = " width:300px;  border-radius:5px;">
                        <div class="header-img">
                        <img src="../../assets/img/menu/' . $row['img'] . '" alt="notif pic" style="width:100%; max-width:100%; min-width:50px;min-height:50px; height:auto;">
                        </div>
                        <div class="body-card" style="padding:10px 20px 10px 20px; text-align:justify; background:#D9D9D9; height:13vh;">
                            <input type="hidden" id="hiddenField" name="name" value="' . $row['name'] . '">
                            <input type="hidden" id="hiddenField" name="price" value="' . $row['price'] . '">
                            <input type="hidden" id="hiddenField" name="dish_id" value="' . $row['dish_id'] . '">
                            <input type="hidden" id="hiddenField" name="img" value="' . $row['img'] . '">
                            <h5 style="font-weight:700;">' . $row['name'] . '</h5>
                            <p style="font-size:12px; color:black; overflow: hidden; margin-top:10px;">' . $row['slogan'] . '</p>
                    </div>
                        <div class="footer-card" style="padding:10px 20px 10px 20px; text-align:center; background:#D9D9D9;">
                             <select class="size" name="size" style="width:100%; text-align:center;" disabled>';
                                        // Split the 'size' data into an array
                                        $sizes = explode(',', $row['size']);

                                        // Iterate over the 'size' data and create an option for each size
                                        foreach ($sizes as $size) {
                                            echo '<option value="' . $size . ' "">' . ucfirst($size) . ' Pan Pizza</option>';
                                        }

                                        echo '
                            </select>
                        <input type = "hidden" id = "hiddenField" name = "size" value = "' . $row['size'] . '">
                        <input type="submit" class="addtobag" id="confirmation" value="Add to Bag - ₱' . $row['price'] . '" name="addtobag" ' . ($hasActiveOrders ? 'disabled' : '') . '>
                            
                </div>  
                </div>
                </div>
</form>
                ';
                                    }
                                } else {

                                    echo '<p style="text-align:center; margin-top:50px;">No Pizza Available Yet</p> ';
                                }
                                ?>


                                <!-- Add more items as needed -->
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!-- ENDING OF BODY -->

            <!-- BEGINNING OF My Bag-->
            <div class="col-sm-2" style="background-color: #efefef;">
                <!-- Add the fill-remaining class -->
                <div class="container" style="margin:0;padding:0;">
                    <div class="row">
                        <div class="col-sm-12" style="<?php echo (!$loggedIn) ? 'margin-bottom:150px;' : ''; ?>">
                            <h3 style="margin-top:35px;margin-left:10px; color:#404040;">My Bag</h3>
                        </div>

                        <?php if ($currentUserId !== '1001'): ?>
                            <form method="post" action="">

                                <div id="deliveryContent" style="display: block;">
                                    <div class="col-sm-12">
                                        <?php if ($loggedIn): ?>
                                            <label for="addressSelect" style="font-weight: bold; color: #333; margin-left: 10px;">Select Address</label>
                                            <select id="addressSelect" name="address" style="font-weight: bold; color: #333; margin-left: 10px;">
                                                <?php
                                                $defaultAddressId = isset($defaultAddressId) ? $defaultAddressId : 1; // Default to ID 1 if not set

                                                // Assuming $addresses is an array of addresses retrieved from the database
                                                foreach ($addresses as $address):
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($address['id']); ?>"
                                                        <?php echo $address['id'] == $defaultAddressId ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($address['name']) . " - " . htmlspecialchars($address['address']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <div class="add-address" style="text-align:center;margin-top:10px;">
                                                <div class="address-btn">
                                                    <a href="addresses.php" onclick="window.open('addresses.php', 'newwindow', 'width=450,height=700'); return false;">Add New Address</a>
                                                </div>
                                            </div>
                                            <input type="text" id="addressInput" name="address"
                                                style="font-weight: bold; color: #333; margin-left: 10px;" readonly>
                                        <?php endif; ?>
                                    </div>
                                </div>


                                <div class="col-sm-12 cart"
                                    style="margin:0 0 -25px 0; padding:0; height:45vh; overflow-y: scroll; overflow:auto; ">

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
                                            <div class = "box" style = "padding: 10px;border-radius:10px; margin: 10px 10px 10px 5px; position:relative; margin-left:10px;">
                                                <div class = "container" style="margin:0; padding:0;">
                                                    <div class ="row">
                                                        <div class = "col-sm-3">
                                                            <div class = "image" style="height:100%; width:100%">
                                                                <img src="../../../src/assets/img/menu/' . $row['img'] . '" alt="notif pic" style="width:100%; max-width:100%; min-width:100px; height:auto; overflow:hidden; border-radius:10px;">
                                                            </div>
                                                        </div>
                                                        <div class = "col-sm-6">
                                                            <div class = "caption">
                                                                <p>' . $row['size'] . ' ' . $row['name'] . '</p>
                                                            </div>
                                                            <div class="edit-btn">
                                                            <a  href="#" class="edit-btn"><i class="fa-solid fa-pencil"  style="font-size:20px;"></i></a> 
                                                            </div>
                                                            <div class="remove-btn">
                                                                <a  href="remove_item.php?remove_item=' . $row['dish_id'] . '" class="remove-btn"><i class="fa-solid fa-xmark" style="font-size:25px;"></i></a> 
                                                            </div>    
                                                        </div>
                                                        <div class = "col-sm-2 bottom-footer">
                                                            <div class = "price">
                                                                <p><span class="price-display" data-id="' . $row['cart_id'] . '">₱' . $row['price'] . '</span></p>
                                                                <input type="hidden" class="price" name="price" data-id="' . $row['cart_id'] . '" value="' . $row['price'] . '">
                                                            
                                                            <div class = "quantity1">
                                                             <div class="edit-btn">
                                                            <a  href="edit_item1.php?edit_item=' . $row['dish_id'] . '" class="edit-btn"><i class="fa-solid fa-pencil"  style="font-size:20px;"></i></a> 
                                                            </div>
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

                                            if ($hasActiveOrders) {
                                                echo '<p style="text-align:center; margin-top:50px;">You have an active order</p>';
                                            } else {
                                                echo '<p style="text-align:center; margin-top:50px;">Add Items to your Bag</p>';
                                            }
                                        }
                                    } else {
                                        echo '<p style="text-align:center; margin-top:150px;">Please Login to Continue</p> ';
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

                                </div>

                                <div class="col-sm-12" style="margin: 30px 0 0 0; <?php echo ($hasActiveOrders) ? 'visibility: hidden;' : ''; ?>">
                                    <div class=" linebreak" style="margin:0 15px 0 5px;">
                                        <hr style="height:2px;">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-6" style="padding:0; margin:0; <?php echo ($hasActiveOrders) ? 'visibility: hidden;' : ''; ?>">
                                                <p style=" font-weight:550">Vatable Sales</p>
                                                <p style="font-weight:550">Vat (12%)</p>
                                                <p style="font-weight:550">Delivery Fee</p>
                                            </div>
                                            <div class="col-sm-6" style="padding:0; margin:0;">
                                                <p id="vatable" style="margin-left: 30px; font-weight:bold;"></p>
                                                <p id="vat" style="margin-left:30px; font-weight:bold;"></p>
                                                <p id="delivery_fee" style="margin-left:30px; font-weight:bold;"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="linebreak" style="margin:0 15px 0 5px; <?php echo ($hasActiveOrders) ? 'visibility: hidden;' : ''; ?>">
                            <hr style="height:2px;">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-6" style="padding:0; margin:0;<?php echo ($hasActiveOrders) ? 'visibility: hidden;' : ''; ?>">
                                    <p style="font-weight:550">Total</p>
                                </div>
                                <div class="col-sm-6" style="padding:0; margin:0;">
                                    <p id="total_amount" style="margin-left:30px; font-weight:bold;"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" style="padding:0 20px 0 20px; ">
                        <?php if (!$hasActiveOrders): ?>
                            <?php if ($isCartEmpty || !$loggedIn): ?>
                                <input type="submit" value="Checkout" class="checkout" name="checkout" disabled>
                            <?php else: ?>
                                <input type="submit" value="Checkout" class="checkout" name="checkout">
                            <?php endif; ?>
                        <?php else : ?>
                            <input type="submit" value="View Order Status" class="checkout" name="view-order">
                        <?php endif; ?>

                        </form>
                    </div>
                </div>

            <?php else: ?>
                <div class="col-sm-12">

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
                            <div class="col-sm-6" style="padding:0; margin:0;">
                                <p style="font-weight:550">Total</p>
                            </div>
                            <div class="col-sm-6" style="padding:0; margin:0;">
                                <p id="total_amount1" style="margin-left:30px; font-weight:bold;">₱ 0
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" style="padding:0 20px 0 20px; margin-top:20px;">
                    <input type="submit" value="Checkout" class="checkout" name="checkout">
                    </form>
                </div>
            </div>


        </div>
    <?php endif; ?>
    </div>
    </div>
    </div>
    <!-- ENDING OF My Bag -->
    </div>
    </div>


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
        }, 3000);
    </script>
    <script>
        <?php if ($isCartEmpty && !$hasActiveOrders) : ?>
            document.getElementById('orderLink').classList.add('disabled');
        <?php endif; ?>
    </script>
    <script>
        // Update the input field when a new address is selected
        document.getElementById('addressSelect').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var addressId = selectedOption.value; // Get the ID of the selected option
            var addressText = selectedOption.text; // Get the text of the selected option (name + address)

            // Split the addressText into name and address. Assuming that name and address are separated by a dash
            var parts = addressText.split(' - ');
            var addressOnly = parts.length > 1 ? parts[1] : addressText; // Get the second part as address

            // Update the input field with the address only
            document.getElementById('addressInput').value = addressOnly;

            // Store the selected address ID in session storage
            sessionStorage.setItem('selectedAddress', addressId);
        });

        window.onload = function() {
            var addressSelect = document.getElementById('addressSelect');
            var addressInput = document.getElementById('addressInput');

            // If there's only one option in the select dropdown
            if (addressSelect.options.length === 1) {
                var singleOption = addressSelect.options[0];
                singleOption.selected = true;

                // Extract the name and address
                var addressText = singleOption.text;
                var parts = addressText.split(' - ');
                var addressOnly = parts.length > 1 ? parts[1] : addressText;

                // Update the input field with the address and session storage
                addressInput.value = addressOnly;
                sessionStorage.setItem('selectedAddress', singleOption.value);
            } else {
                // Handle normal loading with multiple options
                var storedAddressId = sessionStorage.getItem('selectedAddress') || null;
                if (storedAddressId) {
                    for (var i = 0; i < addressSelect.options.length; i++) {
                        if (addressSelect.options[i].value === storedAddressId) {
                            addressSelect.options[i].selected = true;

                            // Extract the name and address
                            var addressText = addressSelect.options[i].text;
                            var parts = addressText.split(' - ');
                            var addressOnly = parts.length > 1 ? parts[1] : addressText;

                            // Update the input field with the address only
                            addressInput.value = addressOnly;
                            break;
                        }
                    }
                }
            }
        };
    </script>


</body>

</html>