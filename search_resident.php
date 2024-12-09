<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT id, firstname, middlename, lastname, suffix, purok FROM resident_list WHERE firstname LIKE ? OR lastname LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Database query preparation failed']);
        exit;
    }

    $search = '%' . $query . '%';
    $stmt->bind_param('ss', $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $residents = [];
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }

    echo json_encode($residents);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
