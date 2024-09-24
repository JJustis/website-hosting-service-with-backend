<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_ticket'])) {
        $subject = $db->real_escape_string($_POST['subject']);
        $message = $db->real_escape_string($_POST['message']);
        
        $db->query("INSERT INTO support_tickets (user_id, subject, message) 
                    VALUES ($user_id, '$subject', '$message')");
    }
}

$tickets = $db->query("SELECT * FROM support_tickets WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>/* Global Styles */
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
    <title>Support Tickets</title>
</head>
<body>
    <h1>Support Tickets</h1>
    
    <h2>Submit a New Ticket</h2>
    <form method="post">
        <input type="text" name="subject" placeholder="Subject" required>
        <textarea name="message" placeholder="Your message" required></textarea>
        <button type="submit" name="submit_ticket">Submit Ticket</button>
    </form>

    <h2>Your Tickets</h2>
    <table border="1">
        <tr>
            <th>Date</th>
            <th>Subject</th>
            <th>Status</th>
        </tr>
        <?php while ($ticket = $tickets->fetch_assoc()): ?>
        <tr>
            <td><?php echo $ticket['created_at']; ?></td>
            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
            <td><?php echo $ticket['status']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>