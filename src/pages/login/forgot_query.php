<?php
session_start();
include 'super_admin/connection/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Validate email
    if (empty($email)) {
        header("Location: login.php?error=Email%20is%20required");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: login.php?error=Invalid%20email%20format");
        exit();
    }

    $sql = "SELECT * FROM accounts WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, proceed with password reset process
        // Generate a unique token
        $token = bin2hex(random_bytes(50));


        // Store the token in the database with an expiration time
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $sql = "UPDATE accounts SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'phcr.inventory@gmail.com'; // Use your own SMTP username
        $mail->Password = 'qumsybajaxdmuyqv'; // Use a secure app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('phcr.inventory@gmail.com', 'PHCR Notifications');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';

        // Constructing the message with a clickable reset link
        $resetLink = "https://www.pizzahut-chinoroces.com/src/pages/login/reset-password.php?token=" . $token;
        $message = '<p>Dear User,</p>';
        $message .= '<p>Click the following link to reset your password:</p>';
        $message .= '<p><a href="' . $resetLink . '" target="_blank">Reset your password</a></p>';
        $message .= '<p>If you did not request this, please ignore this email.</p>';
        $message .= '<br><p>Best regards,</p>';
        $message .= '<p>PHCR Inventory System</p>';

        $mail->Body = $message;

        if ($mail->send()) {
            header("Location: login.php?success=A%20password%20reset%20link%20has%20been%20sent%20to%20your%20email");
            exit();
        } else {
            header("Location: forgot-password.php?error=Failed%20to%20send%20the%20password%20reset%20link");
            exit();
        }
    } else {
        header("Location: forgot-password.php?error=No%20account%20found");
        exit();
    }
}
