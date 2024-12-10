<?php
require 'db_connect.php';

// Get the calamity name from the query string
$calamity = $_GET['calamity'] ?? '';

if (!$calamity) {
    echo '<p>Error: Calamity not specified.</p>';
    exit;
}

// Fetch claimed supplies for the specified calamity
$stmt = $conn->prepare("
    SELECT 
        rr.resident_name, 
        rr.schedule_datetime AS date_time_received,
        GROUP_CONCAT(i.name, ' (', sd.quantity, ' ', i.unit, ')' SEPARATOR ', ') AS supplies_given
    FROM 
        resident_ranking rr
    INNER JOIN 
        supply_distribution sd ON rr.ranking_id = sd.ranking_id
    INNER JOIN 
        inventory i ON sd.supply_id = i.id
    WHERE 
        rr.calamity = ? AND rr.status = 'claimed'
    GROUP BY 
        rr.ranking_id
    ORDER BY 
        rr.schedule_datetime ASC
");
$stmt->bind_param("s", $calamity);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplies Received for <?php echo htmlspecialchars($calamity); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h1 {
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <h1>Supplies Received for <?php echo htmlspecialchars($calamity); ?></h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date & Time Received</th>
                    <th>Supplies Given</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_time_received']); ?></td>
                        <td><?php echo htmlspecialchars($row['supplies_given']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No residents that claimed for this calamity.</p>
    <?php endif; ?>
    <?php
    $stmt->close();
    ?>
</body>

</html>
