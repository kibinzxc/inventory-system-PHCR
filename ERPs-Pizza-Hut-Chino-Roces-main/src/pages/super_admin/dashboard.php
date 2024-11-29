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
       $userTypeQuery = "SELECT user_type FROM users WHERE uid = $currentUserId";
    $result = $conn->query($userTypeQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userType = $row['user_type'];

        // Check if user_type is "customer"
        if ($userType !== "super_admin") {
            header("Location: ../../../admin_login.php");
            exit(); // Ensure script stops execution after redirection
        }
    }
    $conn->close();
} else {
 $loggedIn = false;
        
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../admin_login.php");
    exit();
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


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/pizzahut-logo.png">
    <title>Super Admin | Pizza Hut Chino Roces</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
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
                    <a href="dashboard.php" class="item1">
                        <img class="logo" src="../../assets/img/pizzahut-logo.png" alt="Pizza Hut Logo">
                    </a>
                    <a href="dashboard.php" class="item active">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="items.php" class="item" id="orderLink">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Items</span>
                    </a>
                    <a href="logs.php" class="item">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Accounts</span>
                    </a>
                    <a href="promotion.php" class="item-last" id="messagesLink">
                        <i class="fa-solid fa-file-pen"></i>
                        <span>Promotion</span>
                    </a>
                    <!-- Toggle Login/Logout link -->
                    <a href="profile.php" class="item">
                        <i class="fa-solid fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="dashboard.php?logout=1" class="item">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>


                </div>
            </div>
             <!-- BEGINNING OF BODY -->
            <div class="col-sm-11 wrap" style="padding:15px; height:100vh; background:#f8f8f8;">
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
                        //get the data of total sales by adding the total price of all the orders
                        $servername = "localhost";
                        $username = "root"; 
                        $password = "";
                        $dbname = "ph_db";      
                        $conn = new mysqli($servername, $username, $password, $dbname);     
                        $sql = "SELECT SUM(totalPrice) AS total_sales FROM success_orders";
                        $result = $conn->query($sql);   
                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $totalSales = $row['total_sales'];
                        } else {
                            $totalSales = 0;
                        }
                        //get the data of total orders by counting the number of successful orders
                        $sql = "SELECT COUNT(*) AS total_orders FROM success_orders";  
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $totalOrders = $row['total_orders'];
                        } else {
                            $totalOrders = 0;
                        }
                        //get the data of pending orders by counting the number of pending orders from orders
                        $sql = "SELECT COUNT(*) AS pending_orders FROM orders";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $pendingOrders = $row['pending_orders'];
                        } else {
                            $pendingOrders = 0;
                        }
                        //get the data of total dish by counting the number of dish from dishes
                        $sql = "SELECT COUNT(*) AS total_dish FROM dishes";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $totalDish = $row['total_dish'];
                        } else {
                            $totalDish = 0;
                        }
                        //get the data of total customers by counting the number of customers from users
                        $sql = "SELECT COUNT(*) AS total_customers FROM users WHERE user_type = 'customer'";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $totalCustomers = $row['total_customers'];
                        } else {
                            $totalCustomers = 0;
                        }
                        ?>
                    <div class="col-sm-12">
                        <div class="wrapper">
                            <div class = "wrapper2" style="height:28vh; overflow:auto; padding:20px 20px 10px 20px;">
                                <div class="card" style="margin-left:25px;">
                                    <div class="card-body">
                                        <div>
                                            <h5 class="card-title">Total Sales</h5>
                                            <p class="card-text">₱ <?php echo $totalSales ?></p>
                                        </div>
                                        <div class="money-icon">
                                            <i class="fa-regular fa-money-bill-1"></i>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted" id="lastUpdated">Last updated: <span id="elapsedTime">0</span> minutes ago </small>
                                    </div>
                                </div>
                                    <div class="card">
                                    <div class="card-body">
                                        <div>
                                            <h5 class="card-title">Total Orders</h5>
                                            <p class="card-text"><?php echo $totalOrders?></p>
                                        </div>
                                        <div class="money-icon">
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted" id="lastUpdated">Successful orders only</small>
                                    </div>
                                </div>
                                    <div class="card">
                                    <div class="card-body">
                                        <div>
                                            <h5 class="card-title">Pending Orders</h5>
                                            <p class="card-text"><?php echo $pendingOrders ?></p>
                                        </div>
                                        <div class="money-icon">
                                            <i class="fa-solid fa-bag-shopping"></i>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted" id="lastUpdated">Not delivered yet</small>
                                    </div>
                                </div>
                                    <div class="card">
                                    <div class="card-body">
                                        <div>
                                            <h5 class="card-title">Total Dish</h5>
                                            <p class="card-text"><?php echo $totalDish ?></p>
                                        </div>
                                        <div class="money-icon">
                                            <i class="fa-solid fa-utensils"></i>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted" id="lastUpdated">Recorded dish selections</small>
                                    </div>
                                </div>
                                    <div class="card">
                                    <div class="card-body">
                                        <div>
                                            <h5 class="card-title">Customers</h5>
                                            <p class="card-text"><?php echo $totalCustomers?></p>
                                        </div>
                                        <div class="money-icon">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted" id="lastUpdated">Registered customers in the system</small>
                                    </div>
                                </div>            
                            </div>
                                <div class= "row">
                               <div class = "col-sm-6" style="height:25vh; padding-left:60px; padding-right:20px;">
                                <?php
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

// SQL query to get the top 3 best-selling dishes
$sql = "SELECT name, orders FROM order_count ORDER BY orders DESC LIMIT 3";

$result = $conn->query($sql);

// Display the results in your HTML table
echo '<div class="tableDish">
        <div class="tableDishHeader">
             <h3><span style="color:maroon;"> Best Selling</span> Dish</h3>
        </div>
        <div class="tableDishBody">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" style="text-align:center;">Total Orders</th>
                    </tr>
                </thead>
                <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row["name"] . '</td>
                <td style="text-align:center; ">' . $row["orders"] . '</td>
              </tr>';
    }
} else {
    echo '<tr>
            <td colspan="2">No data available</td>
          </tr>';
}

echo '</tbody>
      </table>
    </div>
  </div>';

// Close the connection
$conn->close();
                                ?>
                                </div>
                                <div class = "col-sm-6" style="height:35vh; padding-right:70px; padding-left:20px;">
<?php
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

// SQL query to get the top 3 best-selling dishes
$sql = "SELECT name, usageCount FROM usage_count ORDER BY usageCount DESC LIMIT 3";

$result = $conn->query($sql);

// Display the results in your HTML table
echo '<div class="tableDish">
        <div class="tableDishHeader">
             <h3><span style="color:maroon;"> Most Used</span> Ingredients</h3>
        </div>
        <div class="tableDishBody">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" style="text-align:center;">Total Orders</th>
                    </tr>
                </thead>
                <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row["name"] . '</td>
                <td style="text-align:center;">' . $row["usageCount"] . '</td>
              </tr>';
    }
} else {
    echo '<tr>
            <td colspan="2">No data available</td>
          </tr>';
}

echo '</tbody>
      </table>
    </div>
  </div>';

// Close the connection
$conn->close();
                                ?>
                                </div>
                               <div class = "col-sm-6" style="height:25vh; padding-left:60px; padding-right:20px;">
                                <?php
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

// SQL query to get the top 3 best-selling dishes
$sql = "SELECT name, orders FROM order_count ORDER BY orders asc LIMIT 3";

$result = $conn->query($sql);

// Display the results in your HTML table
echo '<div class="tableDish">
        <div class="tableDishHeader">
             <h3><span style="color:maroon;"> Least Selling</span> Dish</h3>
        </div>
        <div class="tableDishBody">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" style="text-align:center;">Total Orders</th>
                    </tr>
                </thead>
                <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row["name"] . '</td>
                <td style="text-align:center; ">' . $row["orders"] . '</td>
              </tr>';
    }
} else {
    echo '<tr>
            <td colspan="2">No data available</td>
          </tr>';
}

echo '</tbody>
      </table>
    </div>
  </div>';

// Close the connection
$conn->close();
                                ?>
                                </div>
                               <div class = "col-sm-6" style="height:25vh; padding-right:70px; padding-left:20px">
                                <?php
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

// SQL query to get the top 3 best-selling dishes
$sql = "SELECT name, usageCount FROM usage_count ORDER BY usageCount asc LIMIT 3";

$result = $conn->query($sql);

// Display the results in your HTML table
echo '<div class="tableDish">
        <div class="tableDishHeader">
            <h3><span style="color:maroon;"> Least Used</span> Ingredients</h3>
        </div>
        <div class="tableDishBody">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" style="text-align:center;">Total Orders</th>
                    </tr>
                </thead>
                <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row["name"] . '</td>
                <td style="text-align:center; ">' . $row["usageCount"] . '</td>
              </tr>';
    }
} else {
    echo '<tr>
            <td colspan="2">No data available</td>
          </tr>';
}

echo '</tbody>
      </table>
    </div>
  </div>';

// Close the connection
$conn->close();
                                ?>
                                </div>
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
<script>
        // Function to update the elapsed time
        function updateElapsedTime() {
            // Get the current timestamp in milliseconds
            var currentTime = performance.now();

            // Calculate the elapsed time since the last update in minutes
            var elapsedTime = Math.floor((currentTime - lastUpdateTime) / 1000 / 60);

            // Update the span element with the elapsed time
            document.getElementById('elapsedTime').textContent = elapsedTime;
        }

        // Store the timestamp of the last update
        var lastUpdateTime = performance.now();

        // Call the updateElapsedTime function on page load
        updateElapsedTime();

        // Set up an interval to update the elapsed time every minute
        setInterval(function () {
            updateElapsedTime();
        }, 60000); // 60000 milliseconds = 1 minute
    </script>
</body>

</html>