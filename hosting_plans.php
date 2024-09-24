<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

$db = new mysqli('localhost', 'userx', 'passwordx', 'reservesphp');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $db->real_escape_string($_POST['name']);
        $storage = intval($_POST['storage']);
        $bandwidth = intval($_POST['bandwidth']);
        $price = floatval($_POST['price']);
        $description = $db->real_escape_string($_POST['description']);
        
        $db->query("INSERT INTO hosting_plans (name, storage_limit, bandwidth_limit, price, description) 
                    VALUES ('$name', $storage, $bandwidth, $price, '$description')");
    }
    
    if (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $db->query("DELETE FROM hosting_plans WHERE id = $id");
    }
}

$plans = $db->query("SELECT * FROM hosting_plans");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hosting Plans</title>
</head>
<body>
    <h1>Manage Hosting Plans</h1>
    
    <h2>Add New Plan</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Plan Name" required>
        <input type="number" name="storage" placeholder="Storage Limit (MB)" required>
        <input type="number" name="bandwidth" placeholder="Bandwidth Limit (MB)" required>
        <input type="number" name="price" step="0.01" placeholder="Price" required>
        <textarea name="description" placeholder="Description"></textarea>
        <button type="submit" name="add">Add Plan</button>
    </form>

    <h2>Existing Plans</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Storage (MB)</th>
            <th>Bandwidth (MB)</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($plan = $plans->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($plan['name']); ?></td>
            <td><?php echo $plan['storage_limit']; ?></td>
            <td><?php echo $plan['bandwidth_limit']; ?></td>
            <td>$<?php echo number_format($plan['price'], 2); ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $plan['id']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>