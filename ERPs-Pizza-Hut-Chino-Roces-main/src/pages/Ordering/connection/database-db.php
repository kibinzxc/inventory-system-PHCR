<?php
$servername = "localhost";
$username = "u560143421_kibinzxc";
$password = "Kevin0405!";
$database = "u560143421_phcr_db";

$db = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$db->set_charset("utf8");
