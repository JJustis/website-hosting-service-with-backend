<?php
header('Content-Type: application/json');

// Database connection
$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');
if ($db->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$data = json_decode(file_get_contents('php://input'), true);

$username = $db->real_escape_string($data['username']);
$email = $db->real_escape_string($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Input validation
if (empty($username) || empty($email) || empty($data['password'])) {
    die(json_encode(['success' => false, 'message' => 'All fields are required']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['success' => false, 'message' => 'Invalid email format']));
}

// Check if username or email already exists
$stmt = $db->prepare("SELECT id FROM usershoster WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Username or email already exists']));
}

// Insert new user
$stmt = $db->prepare("INSERT INTO usershoster (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    // Create user directory
    $user_dir = 'user_files/' . $username;
    if (!mkdir($user_dir, 0755, true)) {
        die(json_encode(['success' => false, 'message' => 'Failed to create user directory']));
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}

$stmt->close();
$db->close();
?>