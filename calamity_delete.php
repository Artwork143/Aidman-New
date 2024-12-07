<?php
require 'db_connect.php'; // Include your database connection file

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Query to delete the calamity by ID
    $sql = "DELETE FROM calamity_list WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Calamity record has been deleted successfully.');
            window.location.href = 'calamity_list.php';
        </script>";
    } else {
        echo "<script>
            alert('Error: Unable to delete the calamity record.');
            window.location.href = 'calamity_list.php';
        </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>
        alert('Invalid request. No calamity ID provided.');
        window.location.href = 'calamity_list.php';
    </script>";
}
?>
