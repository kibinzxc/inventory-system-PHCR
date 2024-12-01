<?php
$host = 'localhost';
$dbUsername = 'root';
$dbPassword =  '';
$dbName = 'phcr_db';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
