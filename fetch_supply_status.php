<?php
require 'db_connect.php';

// Fetch all resident IDs that exist in the supply_distribution table
$sql = "SELECT DISTINCT ranking_id FROM supply_distribution";
$result = $conn->query($sql);

$statuses = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statuses[] = [
            'ranking_id' => $row['ranking_id']
        ];
    }
}

echo json_encode(['success' => true, 'statuses' => $statuses]);
?>
