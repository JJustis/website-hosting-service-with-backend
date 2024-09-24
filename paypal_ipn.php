<?php
// Database connection
$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');

// PayPal settings
$paypal_email = 'jandersonjustis@gmail.com';

// STEP 1: Read POST data
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode ('=', $keyval);
    if (count($keyval) == 2)
        $myPost[$keyval[0]] = urldecode($keyval[1]);
}

// Read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
    } else {
        $value = urlencode($value);
    }
    $req .= "&$key=$value";
}

// STEP 2: Post IPN data back to PayPal to validate
$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

$res = curl_exec($ch);
if (!$res) {
    $errno = curl_errno($ch);
    $errstr = curl_error($ch);
    curl_close($ch);
    exit;
}

// STEP 3: Inspect IPN validation result and act accordingly
if (strcmp ($res, "VERIFIED") == 0) {
    // The IPN is verified, process it
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
    $custom = $_POST['custom'];
    
    list($user_id, $plan_id) = explode('|', $custom);
    
    if ($payment_status == 'Completed') {
        // Payment was successful, update the database
        
        // Insert into billing table
        $stmt = $db->prepare("INSERT INTO billing (user_id, amount, description, status) VALUES (?, ?, ?, 'paid')");
        $description = "Payment for " . $item_name;
        $stmt->bind_param("ids", $user_id, $payment_amount, $description);
        $stmt->execute();
        
        // Insert or update user subscription
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+1 year'));
        $stmt = $db->prepare("INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status) 
                              VALUES (?, ?, ?, ?, 'active') 
                              ON DUPLICATE KEY UPDATE plan_id = ?, start_date = ?, end_date = ?, status = 'active'");
        $stmt->bind_param("iisssss", $user_id, $plan_id, $start_date, $end_date, $plan_id, $start_date, $end_date);
        $stmt->execute();
    }
} else if (strcmp ($res, "INVALID") == 0) {
    // IPN invalid, log for manual investigation
    echo "The response from IPN was: <b>" .$res ."</b>";
}
?>