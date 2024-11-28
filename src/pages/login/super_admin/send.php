<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;


if (isset($_POST['submit'])) {
    require __DIR__ . "/vendor/autoload.php";

    // Collect form data
    $message = $_POST['message'];
    $phoneNumber = $_POST['number'];
    $email = $_POST['email'];
    $subject = $_POST['subj'];

    // Send SMS
    $apiURL = "2mj3qm.api.infobip.com";
    $apiKey = "c5f0b39d5bd686fca4dc8fe3d13f3f87-cc707099-4a93-467c-ac53-205bd95c3911";

    $configuration = new Configuration(host: $apiURL, apiKey: $apiKey);
    $api = new SmsApi(config: $configuration);

    $destination = new SmsDestination(to: $phoneNumber);

    $theMessage = new SmsTextualMessage(
        from: "PHCR",
        destinations: [$destination],
        text: $message
    );

    $request = new SmsAdvancedTextualRequest(messages: [$theMessage]);

    try {
        $response = $api->sendSmsMessage($request);
        echo 'SMS Message Sent<br>';
        print_r($response); // Check response details
    } catch (Exception $e) {
        echo 'Failed to send SMS: ' . $e->getMessage() . '<br>';
    }


    // Send Email

    try {

        require '../phpmailer/src/Exception.php';
        require '../phpmailer/src/PHPMailer.php';
        require '../phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'phcr.inventory@gmail.com';
        $mail->Password = 'qumsybajaxdmuyqv'; // Use a secure app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('phcr.inventory@gmail.com', 'PHCR Notifications');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        echo 'Email Message Sent';
    } catch (Exception $e) {
        echo 'Failed to send Email: ' . $mail->ErrorInfo;
    }
}
