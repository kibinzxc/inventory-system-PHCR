<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../src/components/Login/LoginForm.css">
    <link rel="stylesheet" href="../../index.css">
</head>

<body>
    <div class="LoginForm">
        <div class="LoginForm_header">
            <h1>Login</h1>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error_message">
                <img src=" ../../src/assets/alert-circle.svg" alt="">
                <p><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        <?php endif; ?>

        <form action=" ../../src/components/Login/login_query.php" method="POST" class="LoginForm_Contents">
            <div class="username_input">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Email">
            </div>

            <div class="password_input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password">
            </div>

            <div class="LoginForm_Contents_button">
                <button type="submit">Sign in</button>
            </div>

            <div class="LoginForm_Contents_footer">
                <a href="#">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>

</html>