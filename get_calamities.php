<?php
require 'db_connect.php';

if (isset($_GET['type'])) {
    $type = $_GET['type'];

    $stmt = $conn->prepare("SELECT name FROM calamity_list WHERE type = ?");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();

    $calamities = [];
    while ($row = $result->fetch_assoc()) {
        $calamities[] = $row;
    }

    echo json_encode($calamities);
}
?>
