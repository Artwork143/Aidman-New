<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aidman-db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Retrieve user information and role from the database
$sql = "SELECT fullname, email, username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $email, $username, $role);
$stmt->fetch();
$stmt->close();

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/account-info.css">
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="font-awesome/css/all.min.css">
    </head>
<body class="account-info-page">
    <!-- Back to Dashboard button with dynamic URL based on user role -->
    <a href="<?php 
    echo ($role === 'Admin') ? 'admin-dashboard.php' : 
         (($role === 'Official') ? 'official-dashboard.php' : 'resident-dashboard.php'); 
?>" class="account-info-back-button">
    <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

    <div class="account-info-container">
        <h2 class="account-info-title">My Account</h2>
        <div class="account-info-details">
            <div class="account-info-row">
                <i class="fas fa-user"></i>
                <input type="text" value="<?php echo htmlspecialchars($fullname); ?>" readonly>
            </div>
            <div class="account-info-row">
                <i class="fas fa-user-circle"></i>
                <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
            </div>
            <div class="account-info-row">
                <i class="fas fa-envelope"></i>
                <input type="text" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
        </div>
    </div>
</body>
</html>
