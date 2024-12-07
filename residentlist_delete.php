<?php
include 'db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['id'])) {
    $id = $data['id'];

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'No ID provided.']);
        exit;
    }

    // Prepare and execute the delete query
    $sql = "DELETE FROM resident_list WHERE id=?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Record deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
