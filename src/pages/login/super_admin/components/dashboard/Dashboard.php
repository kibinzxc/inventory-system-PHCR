<?php
include '../../authentication/check_login_admin.php';
include '../../connection/database.php';
include 'daily_update.php';
include 'auto-update.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/pizzahut-logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="../../assets/pizzahut-logo.png" type="image/x-icon">
    <link rel="icon" sizes="32x32" href="../../assets/pizzahut-logo.png" type="image/png">
    <link rel="icon" sizes="192x192" href="../../assets/pizzahut-logo.png" type="image/png">
    <title>Dashboard | Pizza Hut</title>

</head>

<body>

    <?php include '../Sidebar/Sidebar.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ?>



    <?php include 'MainContent.php'; ?>


</body>

</html>