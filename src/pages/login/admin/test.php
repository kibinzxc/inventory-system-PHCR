<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    //form sending email with message subj and button
    <form action="send.php" method="post">
        <input type="text" name="number" placeholder="number" value="">
        <input type="email" name="email" placeholder="Email" value="">
        <input type="text" name="subj" placeholder="Subject" value="">
        <textarea name="message" placeholder="Message" value=""></textarea>
        <button type="submit" name="submit">Send</button>
    </form>

</body>

</html>