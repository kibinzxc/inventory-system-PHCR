<?php
$host = 'localhost';
$dbUsername = 'root';
$dbPassword =  '';
$dbName = 'phcr_db';

$db = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$db->set_charset("utf8");
