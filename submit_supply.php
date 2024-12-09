<?php
require 'db_connect.php';

// Check if the form data is properly sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rankingId = $_POST['ranking_id'] ?? null; // Expecting ranking_id instead of resident_id
    $schedule = $_POST['schedule_datetime'] ?? null;
    $supplies = $_POST['supply'] ?? [];

    // Validate that ranking_id, schedule, and supplies are provided
    if (!$rankingId || !$schedule || empty($supplies)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    // Ensure that rankingId and schedule are sanitized
    $rankingId = intval($rankingId);
    $schedule = mysqli_real_escape_string($conn, $schedule);

    // Validate that the ranking_id exists in the resident_ranking table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM resident_ranking WHERE ranking_id = ?");
    $stmt->bind_param("i", $rankingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ranking ID. No such resident ranking found.']);
        exit;
    }

    // Start building the query for inserting supplies
    $query = "INSERT INTO supply_distribution (ranking_id, supply_id, amount, schedule, status) VALUES ";

    // Iterate over supplies to build the values part of the query
    $values = [];
    foreach ($supplies as $supplyId => $amount) {
        // Ensure supplyId and amount are numeric and valid
        $supplyId = intval($supplyId);
        $amount = floatval($amount);  // Use float to handle decimal amounts

        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid supply amount.']);
            exit;
        }

        // Append the values to the query string
        $values[] = "($rankingId, $supplyId, $amount, '$schedule', 'scheduled')";
    }

    // Combine query and values
    $query .= implode(", ", $values);

    // Execute the query
    if ($conn->query($query) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Supplies scheduled successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}
?>
