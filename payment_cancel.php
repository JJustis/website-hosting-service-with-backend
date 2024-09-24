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
    <title>Payment Cancelled</title>
</head>
<body>
    <h1>Payment Cancelled</h1>
    <p>Your payment has been cancelled. If you'd like to try again, please return to the subscription page.</p>
    <a href="user_subscriptions.php">Return to Subscriptions</a>
</body>
</html>