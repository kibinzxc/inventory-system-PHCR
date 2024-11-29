<?php

session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ph_db";

// Create connection
$db= new mysqli($servername, $username, $password, $dbname);

$msgId = $_GET['item_id'];




// sending query
mysqli_query($db,"DELETE FROM users WHERE uid = '".$_GET['item_id']."'");


header("location:logs.php");  

?>
