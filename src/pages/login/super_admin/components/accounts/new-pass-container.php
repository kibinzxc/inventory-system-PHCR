<?php
include '../../authentication/check_login_admin.php';
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
    <title>Accounts | Pizza Hut Chino Roces</title>

</head>

<body>

    <?php include '../Sidebar/Sidebar.php'; ?>


    <?php include 'new-pass.php'; ?>

    <?php include 'SuccessErrorModal.php'; ?>
    <script src="../Sidebar/Sidebar.js"></script>
    <script src="SuccessErrorModal.js"></script>

</body>

</html>