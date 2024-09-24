<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['subscribe'])) {
        $plan_id = intval($_POST['plan_id']);
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+1 year'));
        
        $db->query("INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date) 
                    VALUES ($user_id, $plan_id, '$start_date', '$end_date')");
    }
    
    if (isset($_POST['cancel'])) {
        // Retrieve and sanitize subscription_id
        $subscription_id = intval($_POST['subscription_id']);
        
        // Update subscription status in the database
        $db->query("UPDATE user_subscriptions SET status = 'cancelled' WHERE id = $subscription_id AND user_id = $user_id");

        // Fetch the username from the database
        $result = $db->query("SELECT username FROM users WHERE id = $user_id");
        $row = $result->fetch_assoc();
        $username = $row['username'];

        // Define the directory path based on the username
        $user_directory = "/www/user_files/$username";

        // Use shell command to remove the directory and its contents
        if (is_dir($user_directory)) {
            exec("rm -rf " . escapeshellarg($user_directory));
        }
    }
}

// Fetch available plans and user subscriptions for displaying on the page
$plans = $db->query("SELECT * FROM hosting_plans");
$subscriptions = $db->query("SELECT us.*, hp.name as plan_name 
                             FROM user_subscriptions us 
                             JOIN hosting_plans hp ON us.plan_id = hp.id 
                             WHERE us.user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Subscriptions</title>
   	<style>
	/* Global Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
    margin: 0;
    padding: 0;
    line-height: 1.6;
}

.container {
    width: 80%;
    margin: auto;
    overflow: hidden;
}

/* Navigation Bar */
.navbar {
    background-color: #2c3e50;
    color: #ffffff;
    padding: 10px 0;
    text-align: center;
}

.navbar a {
    color: #ffffff;
    text-decoration: none;
    padding: 15px;
    font-weight: bold;
}

.navbar a:hover {
    background-color: #34495e;
}

/* Lists */
ul {
    list-style: none;
    padding: 0;
}

li {
    background: #ffffff;
    margin: 5px 0;
    padding: 10px;
    border-left: 5px solid #2c3e50;
    transition: all 0.3s ease;
}

li:hover {
    background: #e2e2e2;
    border-left-color: #3498db;
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 16px;
}

table th,
table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #2c3e50;
    color: #ffffff;
}

table tr:nth-child(even) {
    background-color: #f4f4f9;
}

table tr:hover {
    background-color: #e2e2e2;
}

/* Paragraphs */
p {
    margin: 15px 0;
    line-height: 1.8;
}

/* Buttons */
.button {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    outline: none;
    color: #ffffff;
    background-color: #2c3e50;
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px #999;
    transition: all 0.3s ease;
}

.button:hover {
    background-color: #34495e;
}

.button:active {
    background-color: #2c3e50;
    box-shadow: 0 3px #666;
    transform: translateY(2px);
}

/* Forms */
input[type="text"],
input[type="email"],
input[type="password"],
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    margin-top: 6px;
    margin-bottom: 16px;
    resize: vertical;
    border-radius: 4px;
}

input[type="submit"] {
    background-color: #2c3e50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #34495e;
}

/* Support Tickets Section */
.support-ticket {
    border-left: 5px solid #e74c3c;
    margin-bottom: 20px;
    padding: 10px;
    background-color: #ffffff;
}

.support-ticket h3 {
    margin-top: 0;
}

.support-ticket p {
    margin-bottom: 10px;
}

/* Footer */
.footer {
    background-color: #2c3e50;
    color: #ffffff;
    text-align: center;
    padding: 10px 0;
    margin-top: 30px;
}

.footer p {
    margin: 0;
}
</style>
</head>
<body>
    <h1>My Subscriptions</h1>
    
    <h2>Subscribe to a Plan</h2>
    <form method="post">
        <select name="plan_id" required>
            <?php while ($plan = $plans->fetch_assoc()): ?>
            <option value="<?php echo $plan['id']; ?>"><?php echo htmlspecialchars($plan['name']); ?> - $<?php echo number_format($plan['price'], 2); ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="subscribe">Subscribe</button>
    </form>

    <h2>My Active Subscriptions</h2>
    <table border="1">
        <tr>
            <th>Plan</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($sub = $subscriptions->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
            <td><?php echo $sub['start_date']; ?></td>
            <td><?php echo $sub['end_date']; ?></td>
            <td><?php echo $sub['status']; ?></td>
            <td>
                <?php if ($sub['status'] == 'active'): ?>
                <form method="post">
                    <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                    <button type="submit" name="cancel">Cancel</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
