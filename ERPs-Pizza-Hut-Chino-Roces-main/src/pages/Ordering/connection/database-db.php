<?php
$host = 'localhost';
$dbUsername = 'u560143421_kibinzxc';
$dbPassword =  'Kevin0405!';
$dbName = 'u560143421_phcr_db';

// $host = 'localhost';
// $dbUsername = 'root';
// $dbPassword =  '';
// $dbName = 'phcr_db';

$db = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$db->set_charset("utf8");
