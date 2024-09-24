<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Database connection
$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');
if ($db->connect_error) {
    die('Database connection failed');
}

// Get username
$stmt = $db->prepare("SELECT username FROM usershoster WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['username'];

$user_dir = 'user_files/' . $username;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $target_path = $user_dir . '/' . $filename;
    
    // Check file extension
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

// Get list of uploaded files
$uploaded_files = scandir($user_dir);
$uploaded_files = array_diff($uploaded_files, array('.', '..'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile and Content</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    
    <h2>Upload File</h2>
    <form action="upc.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>
    
    <?php
    if (isset($upload_success)) {
        echo "<p style='color: green;'>" . htmlspecialchars($upload_success) . "</p>";
    }
    if (isset($upload_error)) {
        echo "<p style='color: red;'>" . htmlspecialchars($upload_error) . "</p>";
    }
    ?>
    
    <h2>Your Files</h2>
    <ul>
    <?php foreach ($uploaded_files as $file): ?>
        <li><?php echo htmlspecialchars($file); ?></li>
    <?php endforeach; ?>
    </ul>
    
    <a href="logout.php">Logout</a>
</body>
</html>