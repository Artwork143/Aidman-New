<?php
require 'db_connect.php';

$calamity = $_GET['calamity'] ?? '';
if ($calamity) {
    $stmt = $conn->prepare("SELECT * FROM received_supplies WHERE calamity = ?");
    $stmt->bind_param("s", $calamity);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<table border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date & Time Received</th>
                    <th>Supplies Given</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . htmlspecialchars($row['datetime_received']) . '</td>
                <td>' . htmlspecialchars($row['supplies_given']) . '</td>
              </tr>';
    }
    echo '</tbody></table>';
}
