<?php
require 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ranking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$rankingId = $data['ranking_id'];

try {
    $conn->begin_transaction();

    // Update the resident_ranking table status to 'claimed'
    $stmt = $conn->prepare("
        UPDATE resident_ranking
        SET status = 'claimed'
        WHERE ranking_id = ?
    ");
    $stmt->bind_param('i', $rankingId);
    $stmt->execute();

    // Fetch the quantities from supply_distribution for the given ranking_id
    $stmt = $conn->prepare("
        SELECT supply_id, quantity
        FROM supply_distribution
        WHERE ranking_id = ?
    ");
    $stmt->bind_param('i', $rankingId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Deduct the quantities from the inventory table
    while ($row = $result->fetch_assoc()) {
        $supplyId = $row['supply_id'];
        $quantity = $row['quantity'];

        $updateInventoryStmt = $conn->prepare("
            UPDATE inventory
            SET quantity = quantity - ?
            WHERE id = ?
        ");
        $updateInventoryStmt->bind_param('ii', $quantity, $supplyId);
        $updateInventoryStmt->execute();
    }

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Status updated and inventory adjusted successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to mark supply as received: ' . $e->getMessage()]);
}

$conn->close();
?>
