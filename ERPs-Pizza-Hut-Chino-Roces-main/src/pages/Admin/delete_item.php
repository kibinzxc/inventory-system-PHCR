<?php

session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ph_db";

// Create connection
$db= new mysqli($servername, $username, $password, $dbname);

$msgId = $_GET['item_id'];
//retrieve the name of the item
$sql = "SELECT * FROM messages WHERE msgID = $msgId";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$msgName = $row['title'];

// sending query
mysqli_query($db,"DELETE FROM messages WHERE msgID = '".$_GET['item_id']."'");

mysqli_query($db,"DELETE FROM msg_users WHERE title = '$msgName'");
header("location:all_promotions.php");  

?>
