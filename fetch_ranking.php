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
        calamity = ? AND status = 'scheduled'
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

    <div id="modal-notify" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 50%; padding: 20px; background: white; box-shadow: 0px 0px 10px rgba(0,0,0,0.5); z-index: 1000;">
        <div id="modal-notify-content">
            <!-- The notification message will be dynamically inserted here -->
        </div>
        <button id="send-notify-button" class="btn btn-give">Send</button>
        <button onclick="document.getElementById('modal-notify').style.display='none'; document.getElementById('overlay').style.display='none'" class="btn btn-danger">Close</button>
    </div>

    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Fetch existing supply distributions and update button states
        //     fetch('fetch_supply_status.php')
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.success) {
        //                 const supplyStatuses = data.statuses;

        //                 supplyStatuses.forEach(status => {
        //                     const residentRow = document.getElementById('resident-' + status.ranking_id); // Correctly using ranking_id

        //                     if (residentRow) {
        //                         residentRow.querySelector('td:last-child').innerHTML = `
        //                     <button class="btn btn-notify">Notify</button>
        //                     <button class="btn btn-claimed" data-ranking-id="${status.ranking_id}">Claimed</button>
        //                 `;
        //                     }
        //                 });

        //                 // Attach event listeners to "Claimed" buttons
        //                 document.querySelectorAll('.btn-claimed').forEach(button => {
        //                     button.addEventListener('click', function() {
        //                         const rankingId = this.getAttribute('data-ranking-id');

        //                         // Send update request to the server
        //                         fetch('update_supply_status.php', {
        //                                 method: 'POST',
        //                                 headers: {
        //                                     'Content-Type': 'application/json',
        //                                 },
        //                                 body: JSON.stringify({
        //                                     ranking_id: rankingId,
        //                                     status: 'claimed'
        //                                 }),
        //                             })
        //                             .then(response => response.json())
        //                             .then(updateResponse => {
        //                                 if (updateResponse.success) {
        //                                     alert('Status updated to claimed!');
        //                                     this.disabled = true; // Disable the button after claiming
        //                                     this.textContent = 'Claimed';
        //                                 } else {
        //                                     alert('Failed to update status. Please try again.');
        //                                 }
        //                             })
        //                             .catch(error => console.error('Error updating supply status:', error));
        //                     });
        //                 });
        //             }
        //         })
        //         .catch(error => console.error('Error fetching supply statuses:', error));
        // });


        // Show modal with resident's info
        function showGiveSupplyModal(rankingId, residentName) {
            document.getElementById('modal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('resident-id').value = rankingId;
            document.getElementById('resident-name').textContent = residentName;

            console.log("this is ranking_id: " + rankingId);

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

        document.getElementById('give-supply-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission behavior

            // Collect form data
            const formData = new FormData(this);

            // Convert FormData to JSON, including nested supplies
            const data = {};
            formData.forEach((value, key) => {
                if (key.startsWith("supply[")) {
                    // Extract supply ID from the key (e.g., "supply[8]")
                    const supplyId = key.match(/\[([^\]]+)\]/)[1]; // Extract number inside brackets
                    if (!data.supplies) {
                        data.supplies = {};
                    }
                    data.supplies[supplyId] = parseInt(value, 10); // Convert quantity to integer
                } else {
                    data[key] = value;
                }
            });

            // Debugging: Log the final payload being sent
            console.log('Data being sent:', JSON.stringify(data, null, 2));

            // Send POST request to submit supply
            fetch('submit_supply.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data), // Ensure data is serialized to JSON
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Supply submission successful!');
                        closeModal();
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the supply.');
                });
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Fetch existing supply distributions
            fetch('fetch_supply_status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const distributedIds = data.ranking_ids;

                        // Loop through all rows in the table
                        distributedIds.forEach(rankingId => {
                            const row = document.getElementById(`resident-${rankingId}`);
                            if (row) {
                                // Replace the "Give Supply" button with "Notify" and "Received" buttons
                                row.querySelector('td:last-child').innerHTML = `
                            <button class="btn btn-notify" onclick="notifyResident(${rankingId})">Notify</button>
                            <button class="btn btn-danger" onclick="markReceived(${rankingId})">Claimed</button>
                        `;
                            }
                        });
                    } else {
                        console.error('Error fetching supply statuses:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // Function to handle "Notify" action
        function notifyResident(rankingId) {
            document.getElementById('overlay').style.display = 'block';

            fetch(`fetch_supply_distribution.php?ranking_id=${rankingId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const {
                            resident_name: residentName,
                            schedule_datetime: scheduleDatetime,
                            supplies,
                            mobile_number: mobileNumber
                        } = data;

                        // Create the list of supplies
                        const suppliesList = supplies.map(supply => `${supply.quantity} ${supply.unit} of ${supply.name}`).join('<br>');

                        // Create the notification message
                        const message = `
                    <p>Hi Sir/Ma'am <strong>${residentName}</strong>,</p>
                    <p>You can get your Ayuda on <strong>${scheduleDatetime}</strong>.</p>
                    <p>Supplies:</p>
                    <p>${suppliesList}</p>
                `;

                        // Insert the message into the modal content area
                        document.getElementById('modal-notify-content').innerHTML = message;

                        // Display the modal
                        const modal = document.getElementById('modal-notify');
                        modal.style.display = 'block';

                        // Attach the Send button functionality
                        const sendButton = document.getElementById('send-notify-button');
                        sendButton.onclick = function() {
                            sendMessage(mobileNumber, message);
                        };

                    } else {
                        alert(`Error: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Error fetching supply distribution:', error);
                    alert('An error occurred while fetching the supply distribution.');
                });
        }

        // Function to send the SMS to the resident's mobile number
        function sendMessage(mobileNumber, message) {
            // Send the message to the backend (e.g., via an API or SMS service like Twilio)
            fetch('send_sms.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        mobile_number: mobileNumber,
                        message: message
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Message sent successfully!');
                        document.getElementById('modal-notify').style.display = 'none'; // Close the modal
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error sending SMS:', error);
                    alert('An error occurred while sending the message.');
                });
        }
    </script>
</body>

</html>