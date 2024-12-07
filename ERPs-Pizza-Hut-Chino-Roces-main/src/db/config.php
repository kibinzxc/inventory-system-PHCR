<?php
$host = 'localhost';
$dbUsername = 'u560143421_kibinzxc';
$dbPassword =  'Kevin0405!';
$dbName = 'u560143421_phcr_db';

// $host = 'localhost';
// $dbUsername = 'root';g
// $dbPassword =  '';
// $dbName = 'phcr_db';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

//checking

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
