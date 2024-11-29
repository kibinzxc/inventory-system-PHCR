<?php
session_start();
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ph_db";
$db = new mysqli($servername, $username, $password, $dbname);
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
        if ($userType !== "admin") {
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_GET['order_id'];

    // Fetching data from the 'orders' table
    $sqla = "SELECT * FROM orders WHERE orderID = $order_id";
    $resulta = $db->query($sqla);
    
    
     $sqlx = "SELECT * FROM orders where orderID = $order_id";
    $resultx = $db->query($sqlx);
    $resultsx = $db->query($sqlx);
    $rowzx = $resultsx->fetch_assoc();
    $uidx = $rowzx['uid'];
    $sql2x = "SELECT * FROM customerinfo where uid = $uidx";
    $resultzx = $db->query($sql2x);
    $rows2x = $resultzx->fetch_assoc();
    //data are uid, title, category, description, image, status
    $titlex = "Order ID#$order_id Status Update";
    $categoryx = "Order status";
    $descriptionx = 
"Your order is now out for delivery. Our team is on the way to bring you a tasty meal. We appreciate your patience and hope you enjoy your food. If you have any questions or need assistance, feel free to contact us. Thank you for choosing our delivery service!";
    $imagex = "delivery.png";
    $statusx = "unread";
    $sql3x = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES ('$uidx', '$titlex', '$categoryx', '$descriptionx', '$imagex', '$statusx')";
    $result3x = $db->query($sql3x);


    if ($resulta->num_rows > 0) {
        while ($row = $resulta->fetch_assoc()) {
            // Check if the 'items' column is not null
            if ($row['items'] !== null) {
                $items_data = json_decode($row['items'], true);

                // Check if json_decode was successful
                foreach ($items_data as $item) {
                    $name = $item['name'];
                    $qty = $item['qty'];

                    // Update the 'order_count' table based on the quantity
                    $sqlUpdateCount = "UPDATE order_count SET orders = orders + $qty WHERE name = '$name'";
                    $db->query($sqlUpdateCount);
                if ($name == "Supreme") {

                    $sqlUpdateHam = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'HAM'";
                    $db->query($sqlUpdateHam);

                    // Update the 'usageCount' for HAM
                    $sqlUpdateUsageCountHam = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HAM'";
                    $db->query($sqlUpdateUsageCountHam);

                    // Update the 'usage_count' table for PIZZA SAUCE
                    $sqlUpdatePizzaSauce = "UPDATE usage_count SET qty = qty + '90' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdatePizzaSauce);

                    // Update the 'usageCount' for PIZZA SAUCE
                    $sqlUpdateUsageCountPizzaSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdateUsageCountPizzaSauce);

                    // Update the 'usage_count' table for MOZZARELLA
                    $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateMozzarella);

                    // Update the 'usageCount' for MOZZARELLA
                    $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateUsageCountMozzarella);

                    // Update the 'usage_count' table for QUICKMELT CHEESE
                    $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateQuickmeltCheese);

                    // Update the 'usageCount' for QUICKMELT CHEESE
                    $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateUsageCountQuickmeltCheese);

                    // Update the 'usage_count' table for FLOUR
                    $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateFlour);

                    // Update the 'usageCount' for FLOUR
                    $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateUsageCountFlour);

                    // Update the 'usage_count' table for SOYA OIL
                    $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateSoyaOil);

                    // Update the 'usageCount' for SOYA OIL
                    $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateUsageCountSoyaOil);
                    
                     // Update the 'usage_count' table for Dough blend
                    $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough Blend'";
                    $db->query($sqlUpdateDoughBlend);

                    // Update the 'usageCount' for Dough blend
                    $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough Blend'";
                    $db->query($sqlUpdateUsageCountDoughBlend);

                    // Update the 'usage_count' table for PIZZA BOX
                    $sqlUpdatePizzaBox = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'Pizza Box'";
                    $db->query($sqlUpdatePizzaBox);

                    // Update the 'usageCount' for PIZZA BOX
                    $sqlUpdateUsageCountPizzaBox = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Pizza Box'";
                    $db->query($sqlUpdateUsageCountPizzaBox);

                    // Update the 'usage_count' table for BOXLINER
                    $sqlUpdateBoxLiner = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'Boxliner'";
                    $db->query($sqlUpdateBoxLiner);

                    // Update the 'usageCount' for BOXLINER
                    $sqlUpdateUsageCountBoxLiner = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Boxliner'";
                    $db->query($sqlUpdateUsageCountBoxLiner);

                    // Update the 'usage_count' table for HOT SAUCE SACHET
                    $sqlUpdateHotSauceSachet = "UPDATE usage_count SET qty = qty + '2' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateHotSauceSachet);

                    // Update the 'usageCount' for HOT SAUCE SACHET
                    $sqlUpdateUsageCountHotSauceSachet = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateUsageCountHotSauceSachet);
                    
                    // Update the 'usage_count' table for Beef topping
                    $sqlUpdateBeefTopping = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Beef topping'";
                    $db->query($sqlUpdateBeefTopping);

                    // Update the 'usageCount' for Beef topping
                    $sqlUpdateUsageCountBeefTopping = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Beef topping'";
                    $db->query($sqlUpdateUsageCountBeefTopping);

                    // Update the 'usage_count' table for Onions
                    $sqlUpdateOnions = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Onions'";
                    $db->query($sqlUpdateOnions);

                    // Update the 'usageCount' for Onions
                    $sqlUpdateUsageCountOnions = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Onions'";
                    $db->query($sqlUpdateUsageCountOnions);

                    // Update the 'usage_count' table for Pork topping
                    $sqlUpdatePorkTopping = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Pork topping'";
                    $db->query($sqlUpdatePorkTopping);

                    // Update the 'usageCount' for Pork topping
                    $sqlUpdateUsageCountPorkTopping = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Pork topping'";
                    $db->query($sqlUpdateUsageCountPorkTopping);

                    // Update the 'usage_count' table for Mushroom
                    $sqlUpdateMushroom = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Mushroom'";
                    $db->query($sqlUpdateMushroom);

                    // Update the 'usageCount' for Mushroom
                    $sqlUpdateUsageCountMushroom = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Mushroom'";
                    $db->query($sqlUpdateUsageCountMushroom);
                    }
                if ($name == "Bacon Supreme") {
                        // Update the 'usage_count' table for PIZZA SAUCE
                        $sqlUpdatePizzaSauce = "UPDATE usage_count SET qty = qty + '90' WHERE name = 'PIZZA SAUCE'";
                        $db->query($sqlUpdatePizzaSauce);

                        // Update the 'usageCount' for PIZZA SAUCE
                        $sqlUpdateUsageCountPizzaSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA SAUCE'";
                        $db->query($sqlUpdateUsageCountPizzaSauce);

                        // Update the 'usage_count' table for MOZZARELLA
                        $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                        $db->query($sqlUpdateMozzarella);

                        // Update the 'usageCount' for MOZZARELLA
                        $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                        $db->query($sqlUpdateUsageCountMozzarella);

                        // Update the 'usage_count' table for QUICKMELT CHEESE
                        $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                        $db->query($sqlUpdateQuickmeltCheese);

                        // Update the 'usageCount' for QUICKMELT CHEESE
                        $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                        $db->query($sqlUpdateUsageCountQuickmeltCheese);

                        // Update the 'usage_count' table for FLOUR
                        $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                        $db->query($sqlUpdateFlour);

                        // Update the 'usageCount' for FLOUR
                        $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                        $db->query($sqlUpdateUsageCountFlour);

                        // Update the 'usage_count' table for SOYA OIL
                        $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                        $db->query($sqlUpdateSoyaOil);

                        // Update the 'usageCount' for SOYA OIL
                        $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                        $db->query($sqlUpdateUsageCountSoyaOil);

                        // Update the 'usage_count' table for Dough blend
                        $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough blend'";
                        $db->query($sqlUpdateDoughBlend);

                        // Update the 'usageCount' for Dough blend
                        $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough blend'";
                        $db->query($sqlUpdateUsageCountDoughBlend);

                        // Update the 'usage_count' table for PIZZA BOX
                        $sqlUpdatePizzaBox = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'PIZZA BOX'";
                        $db->query($sqlUpdatePizzaBox);

                        // Update the 'usageCount' for PIZZA BOX
                        $sqlUpdateUsageCountPizzaBox = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA BOX'";
                        $db->query($sqlUpdateUsageCountPizzaBox);

                        // Update the 'usage_count' table for BOXLINER
                        $sqlUpdateBoxLiner = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'BOXLINER'";
                        $db->query($sqlUpdateBoxLiner);

                        // Update the 'usageCount' for BOXLINER
                        $sqlUpdateUsageCountBoxLiner = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'BOXLINER'";
                        $db->query($sqlUpdateUsageCountBoxLiner);

                        // Update the 'usage_count' table for HOT SAUCE SACHET
                        $sqlUpdateHotSauceSachet = "UPDATE usage_count SET qty = qty + '2' WHERE name = 'HOT SAUCE SACHET'";
                        $db->query($sqlUpdateHotSauceSachet);

                        // Update the 'usageCount' for HOT SAUCE SACHET
                        $sqlUpdateUsageCountHotSauceSachet = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HOT SAUCE SACHET'";
                        $db->query($sqlUpdateUsageCountHotSauceSachet);

                        // Update the 'usage_count' table for Onions
                        $sqlUpdateOnions = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Onions'";
                        $db->query($sqlUpdateOnions);

                        // Update the 'usageCount' for Onions
                        $sqlUpdateUsageCountOnions = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Onions'";
                        $db->query($sqlUpdateUsageCountOnions);

                        // Update the 'usage_count' table for Mushroom
                        $sqlUpdateMushroom = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'Mushroom'";
                        $db->query($sqlUpdateMushroom);

                        // Update the 'usageCount' for Mushroom
                        $sqlUpdateUsageCountMushroom = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Mushroom'";
                        $db->query($sqlUpdateUsageCountMushroom);

                        // Update the 'usage_count' table for Bellpepper green
                        $sqlUpdateBellpepperGreen = "UPDATE usage_count SET qty = qty + '25' WHERE name = 'Bell Pepper green'";
                        $db->query($sqlUpdateBellpepperGreen);

                        // Update the 'usageCount' for Bellpepper green
                        $sqlUpdateUsageCountBellpepperGreen = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bell Pepper green'";
                        $db->query($sqlUpdateUsageCountBellpepperGreen);

                        // Update the 'usage_count' table for Bellpepper red
                        $sqlUpdateBellpepperRed = "UPDATE usage_count SET qty = qty + '25' WHERE name = 'Bell Pepper red'";
                        $db->query($sqlUpdateBellpepperRed);

                        // Update the 'usageCount' for Bellpepper red
                        $sqlUpdateUsageCountBellpepperRed = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bell Pepper red'";
                        $db->query($sqlUpdateUsageCountBellpepperRed);

                        // Update the 'usage_count' table for Bacon
                        $sqlUpdateBacon = "UPDATE usage_count SET qty = qty + '100' WHERE name = 'Bacon'";
                        $db->query($sqlUpdateBacon);

                        // Update the 'usageCount' for Bacon
                        $sqlUpdateUsageCountBacon = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bacon'";
                        $db->query($sqlUpdateUsageCountBacon);
                    }
                if ($name == "Bacon Margherita") {
                        // Update the 'usage_count' table for MOZZARELLA
                        $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                        $db->query($sqlUpdateMozzarella);

                        // Update the 'usageCount' for MOZZARELLA
                        $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                        $db->query($sqlUpdateUsageCountMozzarella);

                        // Update the 'usage_count' table for QUICKMELT CHEESE
                        $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                        $db->query($sqlUpdateQuickmeltCheese);

                        // Update the 'usageCount' for QUICKMELT CHEESE
                        $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                        $db->query($sqlUpdateUsageCountQuickmeltCheese);

                        // Update the 'usage_count' table for FLOUR
                        $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                        $db->query($sqlUpdateFlour);

                        // Update the 'usageCount' for FLOUR
                        $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                        $db->query($sqlUpdateUsageCountFlour);

                        // Update the 'usage_count' table for SOYA OIL
                        $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                        $db->query($sqlUpdateSoyaOil);

                        // Update the 'usageCount' for SOYA OIL
                        $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                        $db->query($sqlUpdateUsageCountSoyaOil);

                        // Update the 'usage_count' table for Dough blend
                        $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough blend'";
                        $db->query($sqlUpdateDoughBlend);

                        // Update the 'usageCount' for Dough blend
                        $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough blend'";
                        $db->query($sqlUpdateUsageCountDoughBlend);
                        // Update the 'usage_count' table for Tomato
                        $sqlUpdateTomato = "UPDATE usage_count SET qty = qty + '30' WHERE name = 'Tomato'";
                        $db->query($sqlUpdateTomato);

                        // Update the 'usageCount' for Tomato
                        $sqlUpdateUsageCountTomato = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Tomato'";
                        $db->query($sqlUpdateUsageCountTomato);

                        // Update the 'usage_count' table for Basil
                        $sqlUpdateBasil = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Basil'";
                        $db->query($sqlUpdateBasil);

                        // Update the 'usageCount' for Basil
                        $sqlUpdateUsageCountBasil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Basil'";
                        $db->query($sqlUpdateUsageCountBasil);

                        // Update the 'usage_count' table for Cheddar
                        $sqlUpdateCheddar = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Cheddar'";
                        $db->query($sqlUpdateCheddar);

                        // Update the 'usageCount' for Cheddar
                        $sqlUpdateUsageCountCheddar = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Cheddar'";
                        $db->query($sqlUpdateUsageCountCheddar);

                        // Update the 'usage_count' table for Carbonara Sauce
                        $sqlUpdateCarbonaraSauce = "UPDATE usage_count SET qty = qty + '100' WHERE name = 'Carbonara Sauce'";
                        $db->query($sqlUpdateCarbonaraSauce);

                        // Update the 'usageCount' for Carbonara Sauce
                        $sqlUpdateUsageCountCarbonaraSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Carbonara Sauce'";
                        $db->query($sqlUpdateUsageCountCarbonaraSauce);

                        // Update the 'usage_count' table for Bacon
                        $sqlUpdateBacon = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Bacon'";
                        $db->query($sqlUpdateBacon);

                        // Update the 'usageCount' for Bacon
                        $sqlUpdateUsageCountBacon = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bacon'";
                        $db->query($sqlUpdateUsageCountBacon);

                        // Update the 'usage_count' table for Parmesan
                        $sqlUpdateParmesan = "UPDATE usage_count SET qty = qty + '40' WHERE name = 'Parmesan'";
                        $db->query($sqlUpdateParmesan);

                        // Update the 'usageCount' for Parmesan
                        $sqlUpdateUsageCountParmesan = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Parmesan'";
                        $db->query($sqlUpdateUsageCountParmesan);
                    
                    }
                    
                if ($name == "BBQ Chicken Supreme") {
                        // Update the 'usage_count' table for MOZZARELLA
                        $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                        $db->query($sqlUpdateMozzarella);

                        // Update the 'usageCount' for MOZZARELLA
                        $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                        $db->query($sqlUpdateUsageCountMozzarella);

                        // Update the 'usage_count' table for QUICKMELT CHEESE
                        $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                        $db->query($sqlUpdateQuickmeltCheese);

                        // Update the 'usageCount' for QUICKMELT CHEESE
                        $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                        $db->query($sqlUpdateUsageCountQuickmeltCheese);

                        // Update the 'usage_count' table for FLOUR
                        $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                        $db->query($sqlUpdateFlour);

                        // Update the 'usageCount' for FLOUR
                        $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                        $db->query($sqlUpdateUsageCountFlour);

                        // Update the 'usage_count' table for SOYA OIL
                        $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                        $db->query($sqlUpdateSoyaOil);

                        // Update the 'usageCount' for SOYA OIL
                        $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                        $db->query($sqlUpdateUsageCountSoyaOil);

                        // Update the 'usage_count' table for Dough blend
                        $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough blend'";
                        $db->query($sqlUpdateDoughBlend);

                        // Update the 'usageCount' for Dough blend
                        $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough blend'";
                        $db->query($sqlUpdateUsageCountDoughBlend);

                        // Update the 'usage_count' table for PIZZA BOX
                        $sqlUpdatePizzaBox = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'PIZZA BOX'";
                        $db->query($sqlUpdatePizzaBox);

                        // Update the 'usageCount' for PIZZA BOX
                        $sqlUpdateUsageCountPizzaBox = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA BOX'";
                        $db->query($sqlUpdateUsageCountPizzaBox);

                        // Update the 'usage_count' table for BOXLINER
                        $sqlUpdateBoxLiner = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'BOXLINER'";
                        $db->query($sqlUpdateBoxLiner);

                        // Update the 'usageCount' for BOXLINER
                        $sqlUpdateUsageCountBoxLiner = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'BOXLINER'";
                        $db->query($sqlUpdateUsageCountBoxLiner);

                        // Update the 'usage_count' table for HOT SAUCE SACHET
                        $sqlUpdateHotSauceSachet = "UPDATE usage_count SET qty = qty + '2' WHERE name = 'HOT SAUCE SACHET'";
                        $db->query($sqlUpdateHotSauceSachet);

                        // Update the 'usageCount' for HOT SAUCE SACHET
                        $sqlUpdateUsageCountHotSauceSachet = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HOT SAUCE SACHET'";
                        $db->query($sqlUpdateUsageCountHotSauceSachet);

                        // Update the 'usage_count' table for BBQ sauce
                        $sqlUpdateBBQSauce = "UPDATE usage_count SET qty = qty + '100' WHERE name = 'BBQ sauce'";
                        $db->query($sqlUpdateBBQSauce);

                        // Update the 'usageCount' for BBQ sauce
                        $sqlUpdateUsageCountBBQSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'BBQ sauce'";
                        $db->query($sqlUpdateUsageCountBBQSauce);

                        // Update the 'usage_count' table for Chicken chunks
                        $sqlUpdateChickenChunks = "UPDATE usage_count SET qty = qty + '100' WHERE name = 'Chicken chunks'";
                        $db->query($sqlUpdateChickenChunks);

                        // Update the 'usageCount' for Chicken chunks
                        $sqlUpdateUsageCountChickenChunks = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Chicken chunks'";
                        $db->query($sqlUpdateUsageCountChickenChunks);

                        // Update the 'usage_count' table for Mushrooms
                        $sqlUpdateMushrooms = "UPDATE usage_count SET qty = qty + '40' WHERE name = 'Mushrooms'";
                        $db->query($sqlUpdateMushrooms);

                        // Update the 'usageCount' for Mushrooms
                        $sqlUpdateUsageCountMushrooms = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Mushrooms'";
                        $db->query($sqlUpdateUsageCountMushrooms);

                        // Update the 'usage_count' table for Onions
                        $sqlUpdateOnions = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Onions'";
                        $db->query($sqlUpdateOnions);

                        // Update the 'usageCount' for Onions
                        $sqlUpdateUsageCountOnions = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Onions'";
                        $db->query($sqlUpdateUsageCountOnions);

                        // Update the 'usage_count' table for Parsley
                        $sqlUpdateParsley = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Parsley'";
                        $db->query($sqlUpdateParsley);

                        // Update the 'usageCount' for Parsley
                        $sqlUpdateUsageCountParsley = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Parsley'";
                        $db->query($sqlUpdateUsageCountParsley);
                    }
                if ($name == "Bacon Cheeseburger") {
                    // Update the 'usage_count' table for PIZZA SAUCE
                    $sqlUpdatePizzaSauce = "UPDATE usage_count SET qty = qty + '90' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdatePizzaSauce);

                    // Update the 'usageCount' for PIZZA SAUCE
                    $sqlUpdateUsageCountPizzaSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdateUsageCountPizzaSauce);

                    // Update the 'usage_count' table for MOZZARELLA
                    $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateMozzarella);

                    // Update the 'usageCount' for MOZZARELLA
                    $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateUsageCountMozzarella);

                    // Update the 'usage_count' table for QUICKMELT CHEESE
                    $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateQuickmeltCheese);

                    // Update the 'usageCount' for QUICKMELT CHEESE
                    $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateUsageCountQuickmeltCheese);

                    // Update the 'usage_count' table for FLOUR
                    $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateFlour);

                    // Update the 'usageCount' for FLOUR
                    $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateUsageCountFlour);

                    // Update the 'usage_count' table for SOYA OIL
                    $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateSoyaOil);

                    // Update the 'usageCount' for SOYA OIL
                    $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateUsageCountSoyaOil);

                    // Update the 'usage_count' table for Dough blend
                    $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateDoughBlend);

                    // Update the 'usageCount' for Dough blend
                    $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateUsageCountDoughBlend);

                    // Update the 'usage_count' table for PIZZA BOX
                        $sqlUpdatePizzaBox = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'PIZZA BOX'";
                        $db->query($sqlUpdatePizzaBox);

                        // Update the 'usageCount' for PIZZA BOX
                        $sqlUpdateUsageCountPizzaBox = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA BOX'";
                        $db->query($sqlUpdateUsageCountPizzaBox);

                        // Update the 'usage_count' table for BOXLINER
                        $sqlUpdateBoxLiner = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'BOXLINER'";
                        $db->query($sqlUpdateBoxLiner);

                        // Update the 'usageCount' for BOXLINER
                        $sqlUpdateUsageCountBoxLiner = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'BOXLINER'";
                        $db->query($sqlUpdateUsageCountBoxLiner);

                        // Update the 'usage_count' table for HOT SAUCE SACHET
                        $sqlUpdateHotSauceSachet = "UPDATE usage_count SET qty = qty + '2' WHERE name = 'HOT SAUCE SACHET'";
                        $db->query($sqlUpdateHotSauceSachet);

                        // Update the 'usageCount' for HOT SAUCE SACHET
                        $sqlUpdateUsageCountHotSauceSachet = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HOT SAUCE SACHET'";
                        $db->query($sqlUpdateUsageCountHotSauceSachet);

                        // Update the 'usage_count' table for Beef
                        $sqlUpdateBeef = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Beef topping'";
                        $db->query($sqlUpdateBeef);

                        // Update the 'usageCount' for Beef
                        $sqlUpdateUsageCountBeef = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Beef topping'";
                        $db->query($sqlUpdateUsageCountBeef);

                        // Update the 'usage_count' table for Bacon
                        $sqlUpdateBacon = "UPDATE usage_count SET qty = qty + '52' WHERE name = 'Bacon'";
                        $db->query($sqlUpdateBacon);

                        // Update the 'usageCount' for Bacon
                        $sqlUpdateUsageCountBacon = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bacon'";
                        $db->query($sqlUpdateUsageCountBacon);

                        // Update the 'usage_count' table for Cheddar
                        $sqlUpdateCheddar = "UPDATE usage_count SET qty = qty + '52' WHERE name = 'Cheddar'";
                        $db->query($sqlUpdateCheddar);

                        // Update the 'usageCount' for Cheddar
                        $sqlUpdateUsageCountCheddar = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Cheddar'";
                        $db->query($sqlUpdateUsageCountCheddar);

                }
                if ($name == "Pepperoni Lovers") {
                    // Update the 'usage_count' table for PIZZA SAUCE
                    $sqlUpdatePizzaSauce = "UPDATE usage_count SET qty = qty + '90' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdatePizzaSauce);

                    // Update the 'usageCount' for PIZZA SAUCE
                    $sqlUpdateUsageCountPizzaSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdateUsageCountPizzaSauce);

                    // Update the 'usage_count' table for MOZZARELLA
                    $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateMozzarella);

                    // Update the 'usageCount' for MOZZARELLA
                    $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateUsageCountMozzarella);

                    // Update the 'usage_count' table for QUICKMELT CHEESE
                    $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateQuickmeltCheese);

                    // Update the 'usageCount' for QUICKMELT CHEESE
                    $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateUsageCountQuickmeltCheese);

                    // Update the 'usage_count' table for FLOUR
                    $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateFlour);

                    // Update the 'usageCount' for FLOUR
                    $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateUsageCountFlour);

                    // Update the 'usage_count' table for SOYA OIL
                    $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateSoyaOil);

                    // Update the 'usageCount' for SOYA OIL
                    $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateUsageCountSoyaOil);

                    // Update the 'usage_count' table for Dough blend
                    $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateDoughBlend);

                    // Update the 'usageCount' for Dough blend
                    $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateUsageCountDoughBlend);
                    // Update the 'usage_count' table for PIZZA BOX
                    $sqlUpdatePizzaBox = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'PIZZA BOX'";
                    $db->query($sqlUpdatePizzaBox);

                    // Update the 'usageCount' for PIZZA BOX
                    $sqlUpdateUsageCountPizzaBox = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA BOX'";
                    $db->query($sqlUpdateUsageCountPizzaBox);

                    // Update the 'usage_count' table for BOXLINER
                    $sqlUpdateBoxLiner = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'BOXLINER'";
                    $db->query($sqlUpdateBoxLiner);

                    // Update the 'usageCount' for BOXLINER
                    $sqlUpdateUsageCountBoxLiner = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'BOXLINER'";
                    $db->query($sqlUpdateUsageCountBoxLiner);

                    // Update the 'usage_count' table for HOT SAUCE SACHET
                    $sqlUpdateHotSauceSachet = "UPDATE usage_count SET qty = qty + '2' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateHotSauceSachet);

                    // Update the 'usageCount' for HOT SAUCE SACHET
                    $sqlUpdateUsageCountHotSauceSachet = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateUsageCountHotSauceSachet);

                    // Update the 'usage_count' table for Pepperoni
                    $sqlUpdatePepperoni = "UPDATE usage_count SET qty = qty + '75' WHERE name = 'Pepperoni'";
                    $db->query($sqlUpdatePepperoni);

                    // Update the 'usageCount' for Pepperoni
                    $sqlUpdateUsageCountPepperoni = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Pepperoni'";
                    $db->query($sqlUpdateUsageCountPepperoni);

                }
                if ($name == "Veggie Lovers") {
                    // Update the 'usage_count' table for PIZZA SAUCE
                    $sqlUpdatePizzaSauce = "UPDATE usage_count SET qty = qty + '90' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdatePizzaSauce);

                    // Update the 'usageCount' for PIZZA SAUCE
                    $sqlUpdateUsageCountPizzaSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdateUsageCountPizzaSauce);

                    // Update the 'usage_count' table for MOZZARELLA
                    $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateMozzarella);

                    // Update the 'usageCount' for MOZZARELLA
                    $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateUsageCountMozzarella);

                    // Update the 'usage_count' table for QUICKMELT CHEESE
                    $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateQuickmeltCheese);

                    // Update the 'usageCount' for QUICKMELT CHEESE
                    $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateUsageCountQuickmeltCheese);

                    // Update the 'usage_count' table for FLOUR
                    $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateFlour);

                    // Update the 'usageCount' for FLOUR
                    $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateUsageCountFlour);

                    // Update the 'usage_count' table for SOYA OIL
                    $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateSoyaOil);

                    // Update the 'usageCount' for SOYA OIL
                    $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateUsageCountSoyaOil);

                    // Update the 'usage_count' table for Dough blend
                    $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateDoughBlend);

                    // Update the 'usageCount' for Dough blend
                    $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateUsageCountDoughBlend);
        
                    // Update the 'usage_count' table for PIZZA BOX
                    $sqlUpdatePizzaBox = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'PIZZA BOX'";
                    $db->query($sqlUpdatePizzaBox);

                    // Update the 'usageCount' for PIZZA BOX
                    $sqlUpdateUsageCountPizzaBox = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA BOX'";
                    $db->query($sqlUpdateUsageCountPizzaBox);

                    // Update the 'usage_count' table for BOXLINER
                    $sqlUpdateBoxLiner = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'BOXLINER'";
                    $db->query($sqlUpdateBoxLiner);

                    // Update the 'usageCount' for BOXLINER
                    $sqlUpdateUsageCountBoxLiner = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'BOXLINER'";
                    $db->query($sqlUpdateUsageCountBoxLiner);

                    // Update the 'usage_count' table for HOT SAUCE SACHET
                    $sqlUpdateHotSauceSachet = "UPDATE usage_count SET qty = qty + '2' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateHotSauceSachet);

                    // Update the 'usageCount' for HOT SAUCE SACHET
                    $sqlUpdateUsageCountHotSauceSachet = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateUsageCountHotSauceSachet);
                    // Update the 'usage_count' table for Pineapple Tidbits
                    $sqlUpdatePineappleTidbits = "UPDATE usage_count SET qty = qty + '70' WHERE name = 'Pineapple Tidbits'";
                    $db->query($sqlUpdatePineappleTidbits);

                    // Update the 'usageCount' for Pineapple Tidbits
                    $sqlUpdateUsageCountPineappleTidbits = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Pineapple Tidbits'";
                    $db->query($sqlUpdateUsageCountPineappleTidbits);

                    // Update the 'usage_count' table for Bell Pepper Red
                    $sqlUpdateBellPepperRed = "UPDATE usage_count SET qty = qty + '25' WHERE name = 'Bell Pepper Red'";
                    $db->query($sqlUpdateBellPepperRed);

                    // Update the 'usageCount' for Bell Pepper Red
                    $sqlUpdateUsageCountBellPepperRed = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bell Pepper Red'";
                    $db->query($sqlUpdateUsageCountBellPepperRed);

                    // Update the 'usage_count' table for Bell Pepper Green
                    $sqlUpdateBellPepperGreen = "UPDATE usage_count SET qty = qty + '25' WHERE name = 'Bell Pepper Green'";
                    $db->query($sqlUpdateBellPepperGreen);

                    // Update the 'usageCount' for Bell Pepper Green
                    $sqlUpdateUsageCountBellPepperGreen = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bell Pepper Green'";
                    $db->query($sqlUpdateUsageCountBellPepperGreen);

                    // Update the 'usage_count' table for Onions
                    $sqlUpdateOnions = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Onions'";
                    $db->query($sqlUpdateOnions);

                    // Update the 'usageCount' for Onions
                    $sqlUpdateUsageCountOnions = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Onions'";
                    $db->query($sqlUpdateUsageCountOnions);
                }
                if ($name == "Cheese Lovers") {
                    // Update the 'usage_count' table for PIZZA SAUCE
                    $sqlUpdatePizzaSauce = "UPDATE usage_count SET qty = qty + '90' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdatePizzaSauce);

                    // Update the 'usageCount' for PIZZA SAUCE
                    $sqlUpdateUsageCountPizzaSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA SAUCE'";
                    $db->query($sqlUpdateUsageCountPizzaSauce);

                    // Update the 'usage_count' table for MOZZARELLA
                    $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateMozzarella);

                    // Update the 'usageCount' for MOZZARELLA
                    $sqlUpdateUsageCountMozzarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'MOZZARELLA'";
                    $db->query($sqlUpdateUsageCountMozzarella);

                    // Update the 'usage_count' table for QUICKMELT CHEESE
                    $sqlUpdateQuickmeltCheese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateQuickmeltCheese);

                    // Update the 'usageCount' for QUICKMELT CHEESE
                    $sqlUpdateUsageCountQuickmeltCheese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'QUICKMELT CHEESE'";
                    $db->query($sqlUpdateUsageCountQuickmeltCheese);

                    // Update the 'usage_count' table for FLOUR
                    $sqlUpdateFlour = "UPDATE usage_count SET qty = qty + '180' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateFlour);

                    // Update the 'usageCount' for FLOUR
                    $sqlUpdateUsageCountFlour = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'FLOUR'";
                    $db->query($sqlUpdateUsageCountFlour);

                    // Update the 'usage_count' table for SOYA OIL
                    $sqlUpdateSoyaOil = "UPDATE usage_count SET qty = qty + '20' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateSoyaOil);

                    // Update the 'usageCount' for SOYA OIL
                    $sqlUpdateUsageCountSoyaOil = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'SOYA OIL'";
                    $db->query($sqlUpdateUsageCountSoyaOil);

                    // Update the 'usage_count' table for Dough blend
                    $sqlUpdateDoughBlend = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateDoughBlend);

                    // Update the 'usageCount' for Dough blend
                    $sqlUpdateUsageCountDoughBlend = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Dough blend'";
                    $db->query($sqlUpdateUsageCountDoughBlend);

                    // Update the 'usage_count' table for PIZZA BOX
                    $sqlUpdatePizzaBox = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'PIZZA BOX'";
                    $db->query($sqlUpdatePizzaBox);

                    // Update the 'usageCount' for PIZZA BOX
                    $sqlUpdateUsageCountPizzaBox = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'PIZZA BOX'";
                    $db->query($sqlUpdateUsageCountPizzaBox);

                    // Update the 'usage_count' table for BOXLINER
                    $sqlUpdateBoxLiner = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'BOXLINER'";
                    $db->query($sqlUpdateBoxLiner);

                    // Update the 'usageCount' for BOXLINER
                    $sqlUpdateUsageCountBoxLiner = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'BOXLINER'";
                    $db->query($sqlUpdateUsageCountBoxLiner);

                    // Update the 'usage_count' table for HOT SAUCE SACHET
                    $sqlUpdateHotSauceSachet = "UPDATE usage_count SET qty = qty + '2' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateHotSauceSachet);

                    // Update the 'usageCount' for HOT SAUCE SACHET
                    $sqlUpdateUsageCountHotSauceSachet = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HOT SAUCE SACHET'";
                    $db->query($sqlUpdateUsageCountHotSauceSachet);

                    // Update the 'usage_count' table for Cheddar
                    $sqlUpdateCheddar = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Cheddar'";
                    $db->query($sqlUpdateCheddar);

                    // Update the 'usageCount' for Cheddar
                    $sqlUpdateUsageCountCheddar = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Cheddar'";
                    $db->query($sqlUpdateUsageCountCheddar);

                    // Update the 'usage_count' table for Parmesan
                    $sqlUpdateParmesan = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Parmesan'";
                    $db->query($sqlUpdateParmesan);

                    // Update the 'usageCount' for Parmesan
                    $sqlUpdateUsageCountParmesan = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Parmesan'";
                    $db->query($sqlUpdateUsageCountParmesan);
                }
                if ($name == "Baked Ziti") {
                    // Update the 'usage_count' table for Ziti noodles
                    $sqlUpdateZitiNoodles = "UPDATE usage_count SET qty = qty + '120' WHERE name = 'Ziti noodles'";
                    $db->query($sqlUpdateZitiNoodles);

                    // Update the 'usageCount' for Ziti noodles
                    $sqlUpdateUsageCountZitiNoodles = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Ziti noodles'";
                    $db->query($sqlUpdateUsageCountZitiNoodles);

                    // Update the 'usage_count' table for Bolognese sauce
                    $sqlUpdateBologneseSauce = "UPDATE usage_count SET qty = qty + '25' WHERE name = 'Bolognese sauce'";
                    $db->query($sqlUpdateBologneseSauce);

                    // Update the 'usageCount' for Bolognese sauce
                    $sqlUpdateUsageCountBologneseSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bolognese sauce'";
                    $db->query($sqlUpdateUsageCountBologneseSauce);

                    // Update the 'usage_count' table for Carbonara sauce
                    $sqlUpdateCarbonaraSauce = "UPDATE usage_count SET qty = qty + '25' WHERE name = 'Carbonara sauce'";
                    $db->query($sqlUpdateCarbonaraSauce);

                    // Update the 'usageCount' for Carbonara sauce
                    $sqlUpdateUsageCountCarbonaraSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Carbonara sauce'";
                    $db->query($sqlUpdateUsageCountCarbonaraSauce);

                    // Update the 'usage_count' table for Mozarella
                    $sqlUpdateMozarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Mozarella'";
                    $db->query($sqlUpdateMozarella);

                    // Update the 'usageCount' for Mozarella
                    $sqlUpdateUsageCountMozarella = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Mozarella'";
                    $db->query($sqlUpdateUsageCountMozarella);

                    // Update the 'usage_count' table for Parmesan
                    $sqlUpdateParmesan = "UPDATE usage_count SET qty = qty + '10' WHERE name = 'Parmesan'";
                    $db->query($sqlUpdateParmesan);

                    // Update the 'usageCount' for Parmesan
                    $sqlUpdateUsageCountParmesan = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Parmesan'";
                    $db->query($sqlUpdateUsageCountParmesan);

                    // Update the 'usage_count' table for Parsley
                    $sqlUpdateParsley = "UPDATE usage_count SET qty = qty + '5' WHERE name = 'Parsley'";
                    $db->query($sqlUpdateParsley);

                    // Update the 'usageCount' for Parsley
                    $sqlUpdateUsageCountParsley = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Parsley'";
                    $db->query($sqlUpdateUsageCountParsley);
                }
                if ($name == "Baked Carbonara") {
                        // Update the 'usage_count' table for mozzarella
                        $sqlUpdateMozzarella = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Carbonara Sauce'";
                        $db->query($sqlUpdateMozzarella);
                            // Update the 'usageCount' for mozzarella (assuming you have a column named 'usageCount')
                        $sqlUpdateUsageCount = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Carbonara Sauce'";
                        $db->query($sqlUpdateUsageCount);

                         $sqlUpdateMozzarellaa = "UPDATE usage_count SET qty = qty+ '5' WHERE name = 'HAM'";
                        $db->query($sqlUpdateMozzarellaa);
                            // Update the 'usageCount' for mozzarella (assuming you have a column named 'usageCount')
                        $sqlUpdateUsageCounta = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'HAM'";
                        $db->query($sqlUpdateUsageCounta);
                        
                        $sqlUpdateMozzarellab = "UPDATE usage_count SET qty = qty+ '120' WHERE name = 'Pasta'";
                        $db->query($sqlUpdateMozzarellab);
                            // Update the 'usageCount' for mozzarella (assuming you have a column named 'usageCount')
                        $sqlUpdateUsageCountb = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Pasta'";
                        $db->query($sqlUpdateUsageCountb);
                        
                     }
                    if ($name == "Baked Bolognese") {
                        // Update the 'usage_count' table for Bolognese
                        $sqlUpdateBolognese = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Bolognese sauce'";
                        $db->query($sqlUpdateBolognese);

                        // Update the 'usageCount' for Bolognese
                        $sqlUpdateUsageCountBolognese = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bolognese sauce'";
                        $db->query($sqlUpdateUsageCountBolognese);

                        // Update the 'usage_count' table for spaghetti pasta
                        $sqlUpdateSpaghettiPasta = "UPDATE usage_count SET qty = qty + '120' WHERE name = 'spaghetti pasta'";
                        $db->query($sqlUpdateSpaghettiPasta);

                        // Update the 'usageCount' for spaghetti pasta
                        $sqlUpdateUsageCountSpaghettiPasta = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'spaghetti pasta'";
                        $db->query($sqlUpdateUsageCountSpaghettiPasta);
                    }
                    if ($name == "Baked Bolognese with Meatballs") {
                        // Update the 'usage_count' table for Bolognese sauce
                        $sqlUpdateBologneseSauce = "UPDATE usage_count SET qty = qty + '50' WHERE name = 'Bolognese sauce'";
                        $db->query($sqlUpdateBologneseSauce);

                        // Update the 'usageCount' for Bolognese sauce
                        $sqlUpdateUsageCountBologneseSauce = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bolognese sauce'";
                        $db->query($sqlUpdateUsageCountBologneseSauce);

                        // Update the 'usage_count' table for Spaghetti Pasta
                        $sqlUpdateSpaghettiPasta = "UPDATE usage_count SET qty = qty + '120' WHERE name = 'Spaghetti Pasta'";
                        $db->query($sqlUpdateSpaghettiPasta);

                        // Update the 'usageCount' for Spaghetti Pasta
                        $sqlUpdateUsageCountSpaghettiPasta = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Spaghetti Pasta'";
                        $db->query($sqlUpdateUsageCountSpaghettiPasta);

                        // Update the 'usage_count' table for Meatballs
                        $sqlUpdateMeatballs = "UPDATE usage_count SET qty = qty + '3' WHERE name = 'Meatballs'";
                        $db->query($sqlUpdateMeatballs);

                        // Update the 'usageCount' for Meatballs
                        $sqlUpdateUsageCountMeatballs = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Meatballs'";
                        $db->query($sqlUpdateUsageCountMeatballs);
                    }                       
                if ($name == "7-UP") {
                        // Update the 'usage_count' table for mozzarella
                        $sqlUpdate7up = "UPDATE usage_count SET qty = qty + '1' WHERE name = '7-UP'";
                        $db->query($sqlUpdate7up);
                            // Update the 'usageCount' for mozzarella (assuming you have a column named 'usageCount')
                        $sqlUpdateUsageCount1 = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = '7-UP'";
                        $db->query($sqlUpdateUsageCount1);
                     }
                if ($name == "Bottled Water") {
                    // Update the 'usage_count' table for Bottled Water
                    $sqlUpdateBottledWater = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'Bottled Water'";
                    $db->query($sqlUpdateBottledWater);

                    // Update the 'usageCount' for Bottled Water
                    $sqlUpdateUsageCountBottledWater = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Bottled Water'";
                    $db->query($sqlUpdateUsageCountBottledWater);
                }
                if ($name == "Pepsi") {
                    // Update the 'usage_count' table for Pepsi
                    $sqlUpdatePepsi = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'Pepsi'";
                    $db->query($sqlUpdatePepsi);

                    // Update the 'usageCount' for Pepsi
                    $sqlUpdateUsageCountPepsi = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Pepsi'";
                    $db->query($sqlUpdateUsageCountPepsi);
                }

                if ($name == "Mountain Dew") {
                    // Update the 'usage_count' table for Mountain Dew
                    $sqlUpdateMountainDew = "UPDATE usage_count SET qty = qty + '1' WHERE name = 'Mountain Dew'";
                    $db->query($sqlUpdateMountainDew);

                    // Update the 'usageCount' for Mountain Dew
                    $sqlUpdateUsageCountMountainDew = "UPDATE usage_count SET usageCount = usageCount + '1' WHERE name = 'Mountain Dew'";
                    $db->query($sqlUpdateUsageCountMountainDew);
                }                        
                    
                }
            } else {

            }
        }
    } else {

    }
    $order_id= $_GET['order_id'];
    $newStatus = "delivery";   
    $sql = "UPDATE orders SET status='$newStatus' WHERE orderID = $order_id";
        if ($db->query($sql) === TRUE) {
            $_SESSION['success'] = "Order status updated successfully!";
            header("Location: order-preparing.php");
        } else {
            echo "Error updating record: " . $db->error;
        }
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
    <link rel="stylesheet" href="css/logs2.css">
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
                    <a href="dashboard.php" class="item">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="items.php" class="item" id="orderLink">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Items</span>
                    </a>
                    <a href="logs.php" class="item active">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Logs</span>
                    <?php
                            
                            $unreadNotificationCount4 = $unreadNotificationCount4; 
                            
                            if ($unreadNotificationCount4 > 0) {
                                echo '<span class="notification-count4">' . $unreadNotificationCount4 . '</span>';
                            }
                        ?>
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
                    <a href="order_details1.php?logout=1" class="item">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>


                </div>
            </div>
            <!-- BEGINNING OF BODY -->
            <div class="col-sm-11 wrap" style="padding:15px; height:100vh;">
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
                    <div class="col-sm-12">
                        <div class="wrapper">
                            <h2><i class="fa-solid fa-file" style="margin-left:5px;"></i> Order Details</h2>
                            <div class="upper-buttons">
                                <a href="order-preparing.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-arrow-left"></i> Back</a>

                            </div>
                            <hr>
                            <?php
                            // Assuming you have a database connection established as $db and $currentUserId is defined
                            $order_id= $_GET['order_id'];
                            $sql = "SELECT * FROM orders where orderID = $order_id";
                            $result = $db->query($sql);
                            $results = $db->query($sql);
                            $rowz = $results->fetch_assoc();
                                
                            $orderPlaced = $rowz['orderPlaced'];
                            // Create a DateTime object from the input data
                            $dateTime = new DateTime($orderPlaced);
                            // Format date as M-D-Y
                            $date = $dateTime->format("M-d-Y");
        
                            // Format time with AM/PM
                            $time = $dateTime->format("h:i A");
        
                            $orderDelivered= $rowz['orderDelivered'];
                            $dateTime1 = new DateTime($orderDelivered); 
                            $date1 = $dateTime1->format("M-d-Y");
                            $time1 = $dateTime1->format("h:i A");
    
                            $orderStatus = $rowz['status'];
                            function getBadgeClass($status) {
                                switch ($status) {
                                    case "placed":
                                        return "badge bg-secondary";
                                    case "preparing":
                                        return "badge custom-warning";
                                    case "delivery":
                                        return "badge custom1-warning";
                                    case "delivered":
                                        return "badge bg-success";
                                    default:
                                        return "badge bg-secondary"; // Default to secondary if status is not recognized
                                }
                            }
                                // Generate HTML based on the order status
                                $badgeClass = getBadgeClass($orderStatus);
                                $html = "<p  >Order Status: <span class=\"$badgeClass\" style= 'font-size:1rem; color:white'>" . ucfirst($orderStatus) . "</span></p>";
                                function shouldDisplayDeliveryDetails($status) {
                                    return $status === "delivered";
                                }
                                $deliveryDate = $date1;
                                $deliveryTime = $time1;

                                $badgeClass = getBadgeClass($orderStatus);
                                $displayDeliveryDetails = shouldDisplayDeliveryDetails($orderStatus);
                                $displayCancelButton = ($orderStatus === "placed");
                            ?>

                            <div class="col-sm-12 cart"
                                style="padding:20px 350px 20px 350px; position:relative; height:85vh; overflow:auto;">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Order #<?php echo $rowz['orderID']?></h4>
                                        <div class="edit2">
                                            <form action="" method="post">
                                                <button type="submit" class="btn btn-primary"
                                                    style="margin-bottom:30px;">
                                                    <i class="fa-solid fa-truck-fast"></i> Deliver Order
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h5>Order Details</h5>
                                                <div style="padding:10px 0 0 30px">
                                                    <p>Order Date: <?php echo $date?></p>
                                                    <p>Order Time: <?php echo $time?></p>
                                                    <?php echo $html?>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <h5>Delivery Address</h5>
                                                <div style="padding:10px 0 0 30px">
                                                    <p>Name: <?php echo $rowz['name']?></p>
                                                    <p>Address: <?php echo $rowz['address']?></p>
                                                    <?php if ($displayDeliveryDetails): ?>
                                                    <p>Delivery Date: <?php echo $deliveryDate; ?></p>
                                                    <p>Delivery Time: <?php echo $deliveryTime; ?></p>
                                                    <?php endif; ?>
                                                    <p>Contact Number: <?php 
                                                        $uid = $rowz['uid'];
                                                     $sql2 = "SELECT * FROM customerinfo where uid = $uid";
                                                    $resultz = $db->query($sql2);
                                                    $rows2 = $resultz->fetch_assoc();     
                                                    echo $rows2['contactNum'];
                                                     ?></p>
                                                    <p>Delivery Instructions: <?php echo $rowz['del_instruct']?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12" style="margin-top:30px;">
                                                <h5>Order Items</h5>
                                                <table class="table" style="text-align:center;">
                                                    <thead>
                                                        <tr>
                                                            <th>Item Name</th>
                                                            <th>Size</th>
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                            <th>Total Price</th>
                                                        </tr>
                                                    </thead>
                                                    <?php $totalOrderPrice = 0;
                                    if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Check if the 'items' column is not null
                                        if ($row['items'] !== null) {
                                            $items_data = json_decode($row['items'], true);
                                
                                            // Check if json_decode was successful
                                                foreach ($items_data as $item) {
                                                    $name = $item['name'];
                                                    $size = $item['size'];
                                                    $price = $item['price'];
                                                    $qty = $item['qty'];
                                                    $totalPrice = $item['totalPrice'];
                                                     $totalOrderPrice += $totalPrice;
                                                   echo' <tr>
                                                        <td>' . $name . '</td>
                                                        <td>' . $size . '</td>
                                                        <td>₱ ' . $price . '</td>
                                                        <td>' . $qty . '</td>
                                                        <td>₱ ' . $totalPrice . '</td>
                                                    </tr>';
                                                    
                                                    
                                                }
                                               
                                        } else {
                                            // Handle case where 'items' column is null
                                            echo "'items' column is null";
                                        }
                                            }
                                        } else {
                                            echo "0 results";
                                        }

                                                   $deliveryFee = 50;
                                                    $totalAmount = $totalOrderPrice + $deliveryFee;
                                                    
                                            ?>

                                                </table>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12" style="margin-top:20px;">
                                                <h5>Order Summary</h5>
                                                <table class="table" style="text-align:left; margin-top:10px;">
                                                    <tbody>
                                                        <tr class="subtotal">
                                                            <td>Mode of Payment</td>
                                                            <td>Cash on Delivery</td>
                                                        </tr>
                                                        <tr class="subtotal">
                                                            <td>Subtotal</td>
                                                            <td>₱ <?php echo $totalOrderPrice?></td>
                                                        </tr>
                                                        <tr class="lasttotal">
                                                            <td>Delivery Fee</td>
                                                            <td>₱
                                                                <?php echo $deliveryFee?></td>
                                                        </tr>
                                                        <tr class="total">
                                                            <td style="color:maroon;">Total</td>
                                                            <td style="color:maroon;">₱
                                                                <?php echo $totalAmount?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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