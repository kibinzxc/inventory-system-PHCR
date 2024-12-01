<?php
$servername = "localhost";
$username = "u560143421_kibinzxc";
$password = "Kevin0405!";
$database = "u560143421_phcr_db";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
