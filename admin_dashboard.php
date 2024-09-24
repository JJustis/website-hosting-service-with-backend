<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');

$total_users = $db->query("SELECT COUNT(*) as count FROM usershoster")->fetch_assoc()['count'];
$total_plans = $db->query("SELECT COUNT(*) as count FROM hosting_plans")->fetch_assoc()['count'];
$total_subscriptions = $db->query("SELECT COUNT(*) as count FROM user_subscriptions")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HostClone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_plans.php">Manage Hosting Plans</a></li>
                <li><a href="manage_subscriptions.php">Manage Subscriptions</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <div class="stats">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Plans</h3>
                <p><?php echo $total_plans; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Subscriptions</h3>
                <p><?php echo $total_subscriptions; ?></p>
            </div>
        </div>
    </div>
</body>
</html>