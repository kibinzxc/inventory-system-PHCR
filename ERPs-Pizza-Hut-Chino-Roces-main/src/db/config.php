<?php
$servername = "localhost";
$username = "u560143421_kibinzxc";
$password = "Kevin0405!";
$database = "u560143421_phcr_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
