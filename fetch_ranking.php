<?php
require 'db_connect.php';

// Get the calamity name from the query string
$calamity = $_GET['calamity'] ?? '';

if (!$calamity) {
    echo '<p>Error: Calamity not specified.</p>';
    exit;
}

// Fetch rankings for the specified calamity
$stmt = $conn->prepare("
    SELECT 
        ranking_id,
        resident_name, 
        ranking_score 
    FROM 
        resident_ranking 
    WHERE 
        calamity = ?
    ORDER BY 
        ranking_score DESC
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
    <title>Ranking for <?php echo htmlspecialchars($calamity); ?></title>
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

        .btn {
            padding: 8px 12px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-danger {
            background-color: firebrick;
        }

        .btn-give {
            background-color: #28a745;
        }

        .btn-notify,
        .btn-claimed {
            background-color: #007bff;
        }
    </style>
</head>

<body>
    <h1>Ranking for <?php echo htmlspecialchars($calamity); ?></h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Resident Name</th>
                    <th>Ranking Score</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr id="resident-<?php echo $row['ranking_id']; ?>">
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
                        <td><?php echo number_format($row['ranking_score'], 2); ?></td>
                        <td>
                            <button
                                class="btn btn-give"
                                onclick="showGiveSupplyModal(<?php echo $row['ranking_id']; ?>, '<?php echo htmlspecialchars($row['resident_name']); ?>')">
                                Give Supply
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No rankings available for this calamity.</p>
    <?php endif; ?>
    <?php
    $stmt->close();
    ?>

    <!-- Modal -->
    <div id="modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 50%; padding: 20px; background: white; box-shadow: 0px 0px 10px rgba(0,0,0,0.5); z-index: 1000;">
        <h2>Give Supplies</h2>
        <form id="give-supply-form">
            <input type="hidden" id="resident-id" name="ranking_id">
            <div>
                <label>Resident Name:</label>
                <span id="resident-name"></span>
            </div>
            <div>
                <label for="schedule-datetime">Schedule Pickup:</label>
                <input type="datetime-local" id="schedule-datetime" name="schedule_datetime" required>
            </div>
            <div>
                <label for="supply-amount">Supplies</label>
                <div id="inventory-list"></div>
            </div>
            <button type="submit" class="btn btn-give">Submit</button>
            <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>
        </form>
    </div>

    <div id="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;" onclick="closeModal()"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Fetch existing supply distributions and update button states
    fetch('fetch_supply_status.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const supplyStatuses = data.statuses;

                supplyStatuses.forEach(status => {
                    const residentRow = document.getElementById('resident-' + status.ranking_id); // Correctly using ranking_id

                    if (residentRow) {
                        residentRow.querySelector('td:last-child').innerHTML = `
                            <button class="btn btn-notify">Notify</button>
                            <button class="btn btn-claimed">Claimed</button>
                        `;
                    }
                });
            }
        })
        .catch(error => console.error('Error fetching supply statuses:', error));
});

// Show modal with resident's info
function showGiveSupplyModal(rankingId, residentName) {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('resident-id').value = rankingId;
    document.getElementById('resident-name').textContent = residentName;

    console.log(rankingId);

    // Fetch inventory data
    fetch('fetch_inventory.php')
        .then(response => response.json())
        .then(data => {
            const inventoryList = document.getElementById('inventory-list');
            inventoryList.innerHTML = ''; // Clear any existing inventory items
            data.forEach(item => {
                inventoryList.innerHTML += `
                    <div>
                        <label>${item.name} (Available: ${item.quantity})</label>
                        <input type="number" name="supply[${item.id}]" min="0" max="${item.quantity}">
                    </div>
                `;
            });
        })
        .catch(error => console.error('Error fetching inventory:', error));
}

// Close modal
function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

// Submit supply form and update the button state
document.getElementById('give-supply-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    const rankingId = formData.get('ranking_id'); // Use ranking_id, not resident_id

    console.log(rankingId);
    fetch('submit_supply.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const residentRow = document.getElementById('resident-' + rankingId); // Using ranking_id here

                if (residentRow) {
                    residentRow.querySelector('td:last-child').innerHTML = `
                        <button class="btn btn-notify">Notify</button>
                        <button class="btn btn-claimed">Claimed</button>
                    `;
                }
                closeModal();
                alert(data.message); // Optional feedback
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error submitting supply:', error));
});

    </script>
</body>

</html>