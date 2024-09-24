<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');
$user_id = $_SESSION['user_id'];

$user = $db->query("SELECT * FROM usershoster WHERE id = $user_id")->fetch_assoc();
$subscription = $db->query("SELECT us.*, hp.name as plan_name, hp.storage_limit 
                            FROM user_subscriptions us 
                            JOIN hosting_plans hp ON us.plan_id = hp.id 
                            WHERE us.user_id = $user_id AND us.status = 'active'")->fetch_assoc();

// Define user directory
$user_dir = 'user_files/' . $user['username'];

// Create user directory if it doesn't exist
if (!file_exists($user_dir)) {
    mkdir($user_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $target_path = $user_dir . '/' . $filename;
    
    $allowed_extensions = ['html', 'js', 'json'];
    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $upload_error = "Only HTML, JS, and JSON files are allowed.";
    } else {
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $upload_success = "File uploaded successfully.";
        } else {
            $upload_error = "File upload failed.";
        }
    }
}

// Get user files
$user_files = [];
if (is_dir($user_dir)) {
    $user_files = array_diff(scandir($user_dir), array('.', '..'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - HostClone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>HostClone</h1>
            <nav>
                <ul>
                    <li><a href="user_subscriptions.php">My Subscriptions</a></li>
                    <li><a href="billing.php">Billing History</a></li>
                    <li><a href="support_tickets.php">Support Tickets</a></li>
                    <li><a href="logout.php" class="btn">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>Your Current Plan</h3>
                    <?php if ($subscription): ?>
                        <p>Plan: <?php echo htmlspecialchars($subscription['plan_name']); ?></p>
                        <p>Storage Limit: <?php echo $subscription['storage_limit']; ?> MB</p>
						<p><a href="http://betahut.bounceme.net/www/user_files/<?php echo htmlspecialchars($user['username']); ?>">Site: @betahut.bounceme.net/www/user_files/($username)</a></p>
                    <?php else: ?>
                        <p>You don't have an active subscription. <a href="user_subscriptions.php">Subscribe to a plan</a>.</p>
                    <?php endif; ?>
                </div>
                
                <div class="dashboard-card">
                    <h3>Upload File</h3>
                    <?php if (isset($upload_success)) echo "<p class='success'>$upload_success</p>"; ?>
                    <?php if (isset($upload_error)) echo "<p class='error'>$upload_error</p>"; ?>
                    <form method="post" enctype="multipart/form-data">
                        <input type="file" name="file" required>
                        <button type="submit" class="btn">Upload</button>
                    </form>
                </div>
                
                <div class="dashboard-card">
                    <h3>Your Files</h3>
                    <?php if (empty($user_files)): ?>
                        <p>You haven't uploaded any files yet.</p>
                    <?php else: ?>
                        <ul>
                        <?php foreach ($user_files as $file): ?>
                            <li><?php echo htmlspecialchars($file); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 HostClone. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>