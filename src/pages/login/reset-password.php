<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="LoginForm.css">
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="LoginForm">
        <div class="LoginForm_header">
            <h1>Reset Password</h1>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error_message">
                <img class="error_img" src="super_admin/assets/alert-circle.svg" alt="">
                <p><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        <?php endif; ?>

        <form action="reset_password_query.php" method="POST" class="LoginForm_Contents">
            <div class="email_input">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Email">
            </div>

            <div class="password_input">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="New Password">
            </div>

            <div class="password_input">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
            </div>

            <div class="LoginForm_Contents_button">
                <button type="submit">Reset Password</button>
            </div>
        </form>
    </div>
</body>

</html>