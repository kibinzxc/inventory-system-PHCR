<?php
include '../../authentication/check_login_admin.php';
include 'auto-update.php';
include 'auto-lowstock.php';

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
    <title>Orders | Pizza Hut Chino Roces</title>

</head>

<body>

    <?php include '../../components/Sidebar/Sidebar.php'; ?>



    <?php header('location: manage-orders.php'); ?>


</body>

</html>

<script>
    // Save scroll position when the page is about to unload
    window.onbeforeunload = function() {
        sessionStorage.setItem("scrollPosition", window.scrollY);
    };

    // On page load, check if there's a saved scroll position and scroll to it
    window.onload = function() {
        var scrollPosition = sessionStorage.getItem("scrollPosition");
        if (scrollPosition) {
            window.scrollTo(0, scrollPosition);
            sessionStorage.removeItem("scrollPosition"); // Clear scroll position after it's used
        }
    };
</script>