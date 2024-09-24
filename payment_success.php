<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>
<body>
    <h1>Payment Successful</h1>
    <p>Thank you for your payment. Your subscription has been activated.</p>
    <p>You will be redirected to your dashboard shortly.</p>
    <script>
        setTimeout(function() {
            window.location.href = 'dashboard.php';
        }, 5000);
    </script>
</body>
</html>