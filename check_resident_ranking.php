<?php
// Include database connection file
include 'db_connect.php'; // Replace with your actual database connection file

// Check if the resident ID and calamity are provided in the GET request
if (isset($_GET['id']) && isset($_GET['calamity'])) {
    // Sanitize and retrieve the parameters from the request
    $id = intval($_GET['id']);
    $calamity = $_GET['calamity'];

    // Query to check if the resident exists in the resident_ranking table with the specified calamity
    $query = "SELECT COUNT(*) AS count FROM resident_ranking WHERE id = ? AND calamity = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("is", $id, $calamity); // Bind the parameters
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the result and check if the resident is already listed
        if ($row = $result->fetch_assoc()) {
            // Output true if listed, false otherwise
            echo json_encode($row['count'] > 0);
        } else {
            echo json_encode(false); // Default to false if no result is found
        }

        $stmt->close();
    } else {
        echo json_encode(false); // Default to false if query preparation fails
    }
} else {
    echo json_encode(false); // Default to false if required parameters are not provided
}

// Close the database connection
$conn->close();
?>
