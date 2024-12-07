<?php
include 'db_connect.php'; // Ensure your database connection file is included

// Check if the ID is set and is a valid integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM resident_list WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // If successful, redirect back to the list page with a success message
        header("Location: resident_lists.php?deleted=true");
        exit;
    } else {
        // If there was an issue deleting, show an error
        error_log("Error deleting resident with ID: $id - " . $stmt->error); // Log error for debugging
        echo "Error deleting record. Please try again later.";
    }

    $stmt->close();
} else {
    // If no ID was provided or it's invalid, show an error
    echo "Invalid or missing ID for deletion.";
}
?>
