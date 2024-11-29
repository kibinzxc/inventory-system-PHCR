<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];

    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ph_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user information from the database
    $userQuery = "SELECT uid, email, password FROM users WHERE uid = $currentUserId";
    $result = $conn->query($userQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Handle the case when user data is not found
        header("Location: ../../../admin_login.php");
        exit();
    }

    // Close the database connection
    $conn->close();
} else {
    // Redirect to login page if not logged in
    header("Location: ../../../admin_login.php");
    exit();
}
//submit the form to the database self

if (isset($_POST['createPromotion'])) {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ph_db";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Fetch promotion information from the form
    $title = $_POST['title'];
    $description = $_POST['description'];
     $messageid = $_GET['item_id']; 
    // Handle file upload
    $allowedExtensions = ['jpg', 'png'];
    $targetDir = "../../assets/img/";
    $image = $_FILES['image']['name'];
    $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $target = $targetDir . uniqid() . '.' . $imageFileType;

    // Check if the file type is allowed
    if (!in_array($imageFileType, $allowedExtensions)) {
        $_SESSION['error'] = "Only JPG and PNG files are allowed.";
        header("Location: edit_item.php?item_id=" . $messageid ."");
        exit();
    }

    // update promotion in the database
    
    $sql = "UPDATE messages SET title = '$title', description = '$description', image = '$target' WHERE msgID = $messageid";
    $resulting = $conn->query($sql);

    if ($resulting) {
        // Handle image upload
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Get the last inserted ID from the messages table
            $lastInsertedId = $conn->insert_id;

            // Fetch all customer user IDs
            $sqlCustomers = "SELECT uid FROM users WHERE user_type = 'customer'";
            $resultCustomers = $conn->query($sqlCustomers);

            if ($resultCustomers) {
                while ($row = $resultCustomers->fetch_assoc()) {
                    $customerId = $row['uid'];
                    $status = 'unread';

                    // Insert into msg_users table for each customer
                    $sqlMsgUser = "INSERT INTO msg_users (uid,title, category, description, image, status) VALUES ('$customerId','$title', 'Promotion', '$description', '$target','unread')";
                    $resultMsgUser = $conn->query($sqlMsgUser);

                    if (!$resultMsgUser) {
                        // Handle error if insertion fails for a customer
                        $_SESSION['error'] = "Failed to insert into msg_users table for customer with ID $customerId!";
                    }
                }

                // Promotion created successfully for all customers
                $_SESSION['update'] = "Promotion created successfully!";
                header("Location: all_promotions.php");
                exit();
            } else {
                // Handle error if fetching customer IDs fails
                $_SESSION['error'] = "Failed to fetch customer IDs!";
                header("Location: edit_item.php?item_id=" . $messageid ."");
                exit();
            }
        } else {
            // Handle error if image upload fails
            $_SESSION['error'] = "Failed to upload image!";
            header("Location: edit_item.php?item_id=" . $messageid ."");
            exit();
        }
    } else {
        // Handle error if insertion into messages table fails
        $_SESSION['error'] = "Failed to create promotion!";
        header("Location: edit_item.php?item_id=" . $messageid ."");
        exit();
    }
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ph_db";

// Create connection
$db= new mysqli($servername, $username, $password, $dbname);
$queryza = "SELECT COUNT(*) as unread_order FROM orders";
$result41a = $db->query($queryza);

if ($result41a) {
    $row41a = $result41a->fetch_assoc();
    $unreadNotificationCount4 = $row41a['unread_order'];
} else {
    $unreadNotificationCount4 = 0; // Default to 0 if query fails
}
if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../admin_login.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/pizzahut-logo.png">
    <title>Admin | Pizza Hut Chino Roces</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/promotion.css">
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
                    <a href="promotion.php" class="item1">
                        <img class="logo" src="../../assets/img/pizzahut-logo.png" alt="Pizza Hut Logo">
                    </a>
                    <a href="dashboard.php" class="item">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="items.php" class="item" id="orderLink">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Items</span>
                    </a>
                    <a href="logs.php" class="item">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Logs</span>
                    <?php
                            
                            $unreadNotificationCount4 = $unreadNotificationCount4; 
                            
                            if ($unreadNotificationCount4 > 0) {
                                echo '<span class="notification-count4">' . $unreadNotificationCount4 . '</span>';
                            }
                        ?>
                    </a>
                    <a href="promotion.php" class="item-last active" id="messagesLink">
                        <i class="fa-solid fa-file-pen"></i>
                        <span>Promotion</span>
                    </a>
                    <!-- Toggle Login/Logout link -->
                    <a href="profile.php" class="item">
                        <i class="fa-solid fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="edit_item.php?logout=1" class="item">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>


                </div>
            </div>
            <!-- BEGINNING OF BODY -->

            <div class="col-sm-11 wrap" style="padding:15px; height:100vh;">
                <div class="row">
                    <?php
                        if (isset($_SESSION['update']) && !empty($_SESSION['update'])) {
                            echo '<div class="success" id="message-box">';
                            echo $_SESSION['update'];
                            unset($_SESSION['update']);
                            echo '</div>';
                        }
                        if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                            echo '<div class="error" id="message-box">';
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            echo '</div>';
                        }
                        //retrieve the data of the promotion based on the id using get['item_id']
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "ph_db";
                        $conn = new mysqli($servername, $username, $password, $dbname);   
                        $messageid = $_GET['item_id']; 
                        $sql = "SELECT * FROM messages WHERE msgID = $messageid"; 
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                        }
                
                        ?>
                    

                    <div class="col-sm-12">
                        <div class="wrapper">
                           <h2><i class="fa-solid fa-pen-to-square" style="margin-left:5px;"></i> Edit Promotion</h2>
                            <div class="upper-buttons">
                                 <a href="all_promotions.php" class="btn btn-primary" style="margin-top:10px;"><i class="fa-solid fa-list"></i> All Promotions</a>
                            </div>
                            <hr>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="form-group row" style="padding:50px 400px 20px 400px;">
                                    <label for="title" class="col-sm-2 col-form-label">Title</label>
                                    <div class="col-sm-10" style="margin-bottom:20px;">
                                        <input type="text1" class="form-control" id="title" name="title" value = "<?php echo $row['title']?>" required>
                                    </div>
                                   
                                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10" style="margin-bottom:20px;">
                                        <textarea class="form-control" id="description" name="description" required style="height:150px;"><?php echo $row['description']?></textarea>
                                    </div>
                                        <label for="image" class="col-sm-2 col-form-label">Image</label>
                                        <div class="col-sm-10" style="margin-bottom:20px;">
                                            
                                            <input type="file" class="form-control" id="image" name="image" required>
                                            <small id="imageHelp" class="form-text text-muted" >This will be displayed on the messages page of the users.</small>
                                        </div>
                                        <div class = "col-sm-2"></div>
                                        <div class="col-sm-10">
                                            
                                            <button type="submit" class="btn btn-primary" name="createPromotion">Create Promotion</button>
                                            
                                        </div>
                                    </div>
                            </form>
                                    
                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ENDING OF BODY -->
        <script>
        setTimeout(function() {
            var messageBox = document.getElementById('message-box');
            if (messageBox) {
                messageBox.style.display = 'none';
            }
        }, 2000);
        </script>

</body>

</html>