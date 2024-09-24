<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');
$user_id = $_SESSION['user_id'];

// Fetch the selected plan details
$plan_id = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : 0;
$plan = $db->query("SELECT * FROM hosting_plans WHERE id = $plan_id")->fetch_assoc();

if (!$plan) {
    die("Invalid plan selected.");
}

// PayPal settings
$paypal_email = 'your_paypal_email@example.com';
$return_url = 'https://betahut.bounceme.net/payment_success.php';
$cancel_url = 'https://betahut.bounceme.net/payment_cancel.php';
$notify_url = 'https://betahut.bounceme.net/paypal_ipn.php';

// The amount to charge
$amount = $plan['price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Payment</title>
</head>
<body>
    <h1>Complete Your Payment</h1>
    <p>You are about to subscribe to: <?php echo htmlspecialchars($plan['name']); ?></p>
    <p>Price: $<?php echo number_format($amount, 2); ?></p>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?php echo $paypal_email; ?>">
        <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($plan['name']); ?> Subscription">
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="return" value="<?php echo $return_url; ?>">
        <input type="hidden" name="cancel_return" value="<?php echo $cancel_url; ?>">
        <input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>">
        <input type="hidden" name="custom" value="<?php echo $user_id . '|' . $plan_id; ?>">
        <input type="submit" value="Pay with PayPal">
    </form>
</body>
</html>