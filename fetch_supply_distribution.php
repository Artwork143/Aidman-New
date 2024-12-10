<?php
require 'db_connect.php';

$rankingId = $_GET['ranking_id'] ?? '';

if (!$rankingId) {
    echo json_encode(['success' => false, 'message' => 'Ranking ID not provided.']);
    exit;
}

// Prepare the query to fetch schedule_datetime, resident_name, mobile_number, and supplies
$stmt = $conn->prepare("
    SELECT 
        rr.schedule_datetime,
        rr.resident_name,
        rr.mobile_number,
        i.name AS supply_name, 
        sd.quantity, 
        i.unit
    FROM 
        supply_distribution sd
    INNER JOIN 
        inventory i ON sd.supply_id = i.id
    INNER JOIN 
        resident_ranking rr ON sd.ranking_id = rr.ranking_id
    WHERE 
        sd.ranking_id = ?
");
$stmt->bind_param("i", $rankingId);
$stmt->execute();
$result = $stmt->get_result();

$supplies = [];
$scheduleDatetime = '';
$residentName = '';
$mobileNumber = '';

while ($row = $result->fetch_assoc()) {
    // Fetch schedule_datetime, resident_name, and mobile_number once
    if (!$scheduleDatetime) {
        $scheduleDatetime = $row['schedule_datetime'];
    }
    if (!$residentName) {
        $residentName = $row['resident_name'];
    }
    if (!$mobileNumber) {
        $mobileNumber = $row['mobile_number'];
    }

    // Add each supply item to the supplies array
    $supplies[] = [
        'name' => $row['supply_name'],
        'quantity' => $row['quantity'],
        'unit' => $row['unit'],
    ];
}

// Check if supplies or other details are missing
if (empty($supplies) || !$scheduleDatetime || !$residentName || !$mobileNumber) {
    echo json_encode(['success' => false, 'message' => 'No data found for this Ranking ID.']);
} else {
    echo json_encode([
        'success' => true,
        'schedule_datetime' => $scheduleDatetime,
        'resident_name' => $residentName,
        'mobile_number' => $mobileNumber,
        'supplies' => $supplies,
    ]);
}

$stmt->close();
$conn->close();
?>
