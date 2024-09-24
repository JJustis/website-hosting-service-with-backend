<?php
$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');
$plans = $db->query("SELECT * FROM hosting_plans ORDER BY price ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HostClone - Affordable Web Hosting</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>HostClone</h1>
            <nav>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#plans">Plans</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn">Sign Up</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section id="hero">
            <div class="container">
                <h2>Affordable Web Hosting for Everyone</h2>
                <p>Start your online journey with our reliable and easy-to-use hosting services.</p>
                <a href="#plans" class="btn btn-large">Get Started</a>
            </div>
        </section>

        <section id="features">
            <div class="container">
                <h2>Why Choose HostClone?</h2>
                <div class="feature-grid">
                    <div class="feature">
                        <h3>99.9% Uptime</h3>
                        <p>We guarantee your website will be up and running 24/7.</p>
                    </div>
                    <div class="feature">
                        <h3>Easy File Management</h3>
                        <p>Upload and manage your files with our user-friendly interface.</p>
                    </div>
                    <div class="feature">
                        <h3>Free</h3>
                        <p>Secure your website with freedom.</p>
                    </div>
                    <div class="feature">
                        <h3>24/7 Support</h3>
                        <p>Our support team is always ready to help you with any issues.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="plans">
            <div class="container">
                <h2>Choose Your Plan</h2>
                <div class="plan-grid">
                    <?php while ($plan = $plans->fetch_assoc()): ?>
                    <div class="plan">
                        <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <p class="price">$<?php echo number_format($plan['price'], 2); ?>/month</p>
                        <ul>
                            <li><?php echo $plan['storage_limit']; ?> MB Storage</li>
                            <li><?php echo $plan['bandwidth_limit']; ?> MB Bandwidth</li>
                            
                            <li>24/7 Support</li>
                        </ul>
                        <a href="register.php?plan=<?php echo $plan['id']; ?>" class="btn">Choose Plan</a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 HostClone. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>