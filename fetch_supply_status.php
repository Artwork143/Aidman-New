<?php
require 'db_connect.php';

$result = $conn->query("SELECT DISTINCT ranking_id FROM supply_distribution");

if ($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['ranking_id'];
    }
    echo json_encode(['success' => true, 'ranking_ids' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch supply statuses.']);
}

$conn->close();
?>
