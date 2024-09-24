<?php
session_start();
$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $email = $db->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $plan_id = intval($_POST['plan_id']);

    // Check if username or email already exists
    $check = $db->query("SELECT id FROM usershoster WHERE username = '$username' OR email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Username or email already exists";
    } else {
        // Insert new user
        $db->query("INSERT INTO usershoster (username, email, password) VALUES ('$username', '$email', '$password')");
        $user_id = $db->insert_id;

        // Create user directory
        $user_dir = 'user_files/' . $username;
        if (!mkdir($user_dir, 0755, true)) {
            $error = "Failed to create user directory";
        } else {
            // Create subscription
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+1 month'));
            $db->query("INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status) 
                        VALUES ($user_id, $plan_id, '$start_date', '$end_date', 'active')");

            $_SESSION['user_id'] = $user_id;
            header('Location: user_dashboard.php');
            exit();
        }
    }
}

// Fetch available plans
$plans = $db->query("SELECT * FROM hosting_plans ORDER BY price ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - HostClone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Register for HostClone</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="plan_id" required>
                <option value="">Select a hosting plan</option>
                <?php while ($plan = $plans->fetch_assoc()): ?>
                    <option value="<?php echo $plan['id']; ?>">
                        <?php echo htmlspecialchars($plan['name']); ?> - 
                        $<?php echo number_format($plan['price'], 2); ?>/month
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>