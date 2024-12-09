<?php
require 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ranking_id'], $data['schedule_datetime'], $data['supplies']) || empty($data['supplies'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$rankingId = $data['ranking_id'];
$scheduleDatetime = $data['schedule_datetime'];
$supplies = $data['supplies'];

try {
    $conn->begin_transaction();

    // Update schedule_datetime in resident_ranking
    $stmt = $conn->prepare("
        UPDATE resident_ranking
        SET schedule_datetime = ?
        WHERE ranking_id = ?
    ");
    $stmt->bind_param('si', $scheduleDatetime, $rankingId);
    $stmt->execute();

    // Insert supplies into supply_distribution
    foreach ($supplies as $supplyId => $quantity) {
        if ($quantity > 0) {
            $stmt = $conn->prepare("
                INSERT INTO supply_distribution (ranking_id, supply_id, quantity)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param('iii', $rankingId, $supplyId, $quantity);
            $stmt->execute();
        }
    }

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Supplies and schedule submitted successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to submit supplies and schedule: ' . $e->getMessage()]);
}

$conn->close();
?>
