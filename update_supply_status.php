<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['ranking_id'], $input['status'])) {
        $ranking_id = $input['ranking_id'];
        $status = $input['status'];

        // Update the database
        // Replace with your database connection and query
        $conn = new mysqli('localhost', 'root', '', 'aidman-db');

        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Database connection error.']);
            exit;
        }

        $stmt = $conn->prepare('UPDATE supply_distribution SET status = ? WHERE ranking_id = ?');
        $stmt->bind_param('si', $status, $ranking_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}
?>
