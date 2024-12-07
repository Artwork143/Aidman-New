<?php
// delete_data.php

// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=aidman-db', 'root', '');

// Check if an ID was provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the delete statement
    $stmt = $pdo->prepare('DELETE FROM residents WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect to the dashboard after successful deletion
        header('Location: official-aid-dashboard.php?delete=success');
    } else {
        // Redirect with an error message if deletion fails
        header('Location: official-dashboard.php?delete=error');
    }
} else {
    // Redirect to the dashboard if no ID was provided
    header('Location: official-dashboard.php');
}
?>
