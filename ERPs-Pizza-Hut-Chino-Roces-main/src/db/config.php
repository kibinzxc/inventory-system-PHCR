<?php
$host = 'localhost';
$dbUsername = 'u560143421_kibinzxc';
$dbPassword =  'Kevin0405!';
$dbName = 'u560143421_phcr_db';


$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
