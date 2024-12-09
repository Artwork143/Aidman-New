<?php include 'check_admin.php'; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
    <link rel="stylesheet" href="font-awesome/css/all.min.css">

    <style>
        .suggestions {
            position: absolute;
            background-color: #0A97B0;
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            color: white;
        }

        .suggestion-item {
            padding: 10px 50px;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #7AB2D3;
            color: black;
        }

        .card {
            width: 60%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .divider {
            margin: 20px 0;
            border: 1px solid #ddd;
        }

        .data-analytics {
            margin-top: 20px;
        }

        .data-analytics h4 {
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
        }

        .data-analytics .form-group {
            margin-bottom: 10px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-bell .badge {
            position: absolute;
            top: -10px;
            right: -12px;
            height: 7px;
            width: 7px;
            background-color: red;
            color: white;
            border-radius: 50%;
            border: 2px solid white;
            padding: 5px;
            font-size: 12px;
            padding-bottom: 10px;
        }

        .dropdown-menu.notifications {
            display: none;
            position: absolute;
            right: 0;
            top: 30px;
            background-color: white;
            border: 1px solid #ddd;
            width: 300px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .dropdown-menu.notifications.show {
            display: block;
        }

        .dropdown-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .form-group-row {
            display: flex;
            gap: 20px;
            /* Adjust spacing between dropdowns */
            align-items: center;
            /* Vertically align labels and dropdowns */
        }

        .form-group-row .form-group {
            flex: 1;
            /* Ensures equal width for both dropdowns */
        }
    </style>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.jpg" alt="Barangay Logo">
                <h1>AIDMAN</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item active"><a href="ranking_resident.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li><a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
                    <li class="arrow-dropdown">
                        <div class="arrow-dropdown-toggle" id="account-control-link">
                            <a href="account_control.php" style="flex-grow: 1;"><i class="fas fa-user-cog mr-2"></i> Account Control Panel Register</a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>
                        <div class="arrow-dropdown-content">
                            <a href="account-management.php"><i class="fa-solid fa-file-invoice"></i> Account Management</a>
                        </div>
                    </li>
                    <li><a href="resident_lists.php"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Resident List</a></li>
                    <li><a href="purok-page2.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Purok List</a></li>
                    <li><a href="calamity_list.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Calamity List</a></li>

                </ul>
            </nav>
        </aside>
        <main>
            <header>
                <h2>Administrator</h2>
                <div class="header-right">
                    <!-- Notification Bell -->
                    <div class="notification-bell" id="notification-bell">
                        <?php
                        // Fetch low-stock inventory items
                        require 'db_connect.php';
                        $sql = "SELECT name, quantity, threshold_quantity FROM inventory WHERE quantity <= threshold_quantity";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $lowStockNotifications = [];
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $lowStockNotifications[] = [
                                    'name' => $row['name'],
                                    'quantity' => $row['quantity'],
                                    'threshold' => $row['threshold_quantity'],
                                ];
                            }
                        }

                        $hasLowStock = !empty($lowStockNotifications);
                        ?>
                        <i class="fas fa-bell" style="color: <?php echo $hasLowStock ? 'red' : '#555'; ?>;"></i>
                        <?php if ($hasLowStock): ?>
                            <span class="badge"><?php echo count($lowStockNotifications); ?></span>
                        <?php endif; ?>
                        <div class="dropdown-menu notifications" id="notification-dropdown">
                            <?php if ($hasLowStock): ?>
                                <?php foreach ($lowStockNotifications as $notification): ?>
                                    <div class="dropdown-item" onclick="location.href='inventory-dashboard.php';" style="cursor: pointer;">
                                        <p>
                                            <strong>Low Stock Alert:</strong> <?php echo htmlspecialchars($notification['name']); ?><br>
                                            Current Quantity: <?php echo htmlspecialchars($notification['quantity']); ?><br>
                                            Restock Threshold: <?php echo htmlspecialchars($notification['threshold']); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dropdown-item">
                                    <p>No low-stock alerts</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="profile-dropdown" id="profile-dropdown">
                        <i class="fas fa-user-circle"></i>
                        <div class="dropdown-menu profile-menu" id="profile-menu">
                            <a href="account-information.php" id="view-profile" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Account Info</span>
                            </a>
                            <a href="./email-inbox.html" class="dropdown-item">
                                <i class="fas fa-envelope-open"></i>
                                <span>Inbox</span>
                            </a>
                            <a href="login.php" id="logout-link" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- "PUT IT HERE" Section -->
            <section class="card-container">
                <div class="card">
                    <h3>Register Ranking For Resident</h3>
                    <form action="process_ranking.php" method="POST">
                        <!-- Calamity Dropdown -->
                        <div class="form-group-row">
                            <div class="form-group">
                                <label for="type-of-calamity">Type of Calamity:</label>
                                <select id="type-of-calamity" name="type_of_calamity" required>
                                    <option value="">-- Select Calamity Type --</option>
                                    <option value="Typhoon">Typhoon</option>
                                    <option value="Flood">Flood</option>
                                    <option value="Earthquake">Earthquake</option>
                                    <option value="Fire Incident">Fire Incident</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="calamity">Calamity:</label>
                                <select id="calamity" name="calamity" required>
                                    <option value="">-- Select a Calamity --</option>
                                </select>
                            </div>

                        </div>

                        <!-- Resident Name Input -->
                        <div class="form-group">
                            <label for="resident-name">Resident Name:</label>
                            <input type="text" id="resident-name" name="resident_name" placeholder="Search Resident Name" autocomplete="off" required>
                            <div id="resident-suggestions" class="suggestions"></div>
                        </div>

                        <!-- Purok and Mobile Number Fields -->
                        <div class="form-group">
                            <label for="purok">Purok:</label>
                            <input type="text" id="purok" name="purok" placeholder="Auto-populated" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="mobile-number">Mobile Number:</label>
                            <input type="number" id="mobile-number" name="mobile_number" placeholder="Enter Mobile Number" required>
                        </div>

                        <!-- Divider -->
                        <hr class="divider">

                        <!-- Data Analytics Section -->
                        <div class="data-analytics">
                            <h4>Data Analytics Using Criteria</h4>
                            <div class="form-group">
                                <label for="damage-severity">Damage Severity (30%):</label>
                                <input type="number" id="damage-severity" name="damage_severity" min="0" max="100" placeholder="Enter Damage Severity" required>
                            </div>

                            <div class="form-group">
                                <label for="occupants">Number of Occupants (20%):</label>
                                <input type="number" id="occupants" name="occupants" min="0" placeholder="Enter Number of Occupants" required>
                            </div>

                            <div class="form-group">
                                <label for="vulnerability">Vulnerability (20%):</label>
                                <input type="number" id="vulnerability" name="vulnerability" min="0" max="100" placeholder="Enter Vulnerability" required>
                            </div>

                            <div class="form-group">
                                <label for="income-level">Income Level (15%):</label>
                                <input type="number" id="income-level" name="income_level" min="0" max="100" placeholder="Enter Income Level" required>
                            </div>

                            <div class="form-group">
                                <label for="special-needs">Special Needs (15%):</label>
                                <input type="number" id="special-needs" name="special_needs" min="0" max="100" placeholder="Enter Special Needs" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn">Submit</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
    <script src="js/admin-dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeOfCalamityDropdown = document.getElementById('type-of-calamity');
            const calamityDropdown = document.getElementById('calamity');

            // Fetch calamity data from the server
            const fetchCalamityData = async (type) => {
                try {
                    const response = await fetch(`get_calamities.php?type=${encodeURIComponent(type)}`);
                    const calamities = await response.json();
                    return calamities;
                } catch (error) {
                    console.error('Error fetching calamity data:', error);
                    return [];
                }
            };

            // Update calamity dropdown based on selected type
            typeOfCalamityDropdown.addEventListener('change', async function() {
                const selectedType = typeOfCalamityDropdown.value;
                calamityDropdown.innerHTML = '<option value="">-- Select a Calamity --</option>';

                if (selectedType) {
                    const calamities = await fetchCalamityData(selectedType);

                    if (calamities.length > 0) {
                        calamities.forEach(calamity => {
                            const option = document.createElement('option');
                            option.value = calamity.name;
                            option.textContent = calamity.name;
                            calamityDropdown.appendChild(option);
                        });
                    } else {
                        calamityDropdown.innerHTML = '<option value="">No calamities available</option>';
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Notification Dropdown
            const notificationBell = document.getElementById('notification-bell');
            const notificationDropdown = document.getElementById('notification-dropdown');
            if (notificationBell) {
                notificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('show');

                    // Close profile menu if open
                    const profileMenu = document.getElementById('profile-menu');
                    if (profileMenu && profileMenu.classList.contains('show')) {
                        profileMenu.classList.remove('show');
                    }
                });
            }

            // Profile Dropdown
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileMenu = document.getElementById('profile-menu');
            if (profileDropdown) {
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');

                    // Close notification dropdown if open
                    if (notificationDropdown && notificationDropdown.classList.contains('show')) {
                        notificationDropdown.classList.remove('show');
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                if (notificationDropdown) notificationDropdown.classList.remove('show');
                if (profileMenu) profileMenu.classList.remove('show');
            });

            // Logout Modal
            const logoutLink = document.getElementById('logout-link');
            const modal = document.getElementById('logout-modal');
            const closeModal = document.querySelector('#logout-modal .close');
            const confirmLogout = document.getElementById('confirm-logout');
            const cancelLogout = document.getElementById('cancel-logout');

            if (logoutLink && modal) {
                logoutLink.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent immediate navigation
                    modal.style.display = 'block'; // Show the modal
                });
            }

            if (closeModal) {
                closeModal.addEventListener('click', () => {
                    modal.style.display = 'none'; // Hide the modal
                });
            }

            if (cancelLogout) {
                cancelLogout.addEventListener('click', () => {
                    modal.style.display = 'none'; // Hide the modal
                });
            }

            if (confirmLogout) {
                confirmLogout.addEventListener('click', () => {
                    window.location.href = logoutLink.href; // Redirect to logout URL
                });
            }

            // Close the modal if clicking outside of it
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.style.display = 'none'; // Hide the modal
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const residentInput = document.getElementById('resident-name');
            const suggestionsDiv = document.getElementById('resident-suggestions');
            const purokInput = document.getElementById('purok');
            const calamitySelect = document.getElementById('calamity'); // Assuming there's a calamity dropdown

            // Fetch resident suggestions as user types
            residentInput.addEventListener('input', function() {
                const query = residentInput.value.trim();

                if (query.length < 2) {
                    suggestionsDiv.innerHTML = ''; // Clear suggestions for short queries
                    return;
                }

                fetch(`search_resident.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsDiv.innerHTML = ''; // Clear previous suggestions
                        if (data.length === 0) {
                            suggestionsDiv.innerHTML = '<p style="color: white; margin:10px 5px;">No residents found</p>';
                        } else {
                            data.forEach(resident => {
                                const suggestion = document.createElement('div');
                                suggestion.classList.add('suggestion-item');
                                suggestion.textContent = `${resident.firstname} ${resident.middlename} ${resident.lastname} ${resident.suffix}`;
                                suggestion.dataset.purok = resident.purok;
                                suggestion.dataset.residentId = resident.id; // Add resident ID for further checks

                                suggestion.addEventListener('click', function() {
                                    const calamity = calamitySelect.value; // Get the selected calamity

                                    if (!calamity) {
                                        alert('Please select a calamity first.');
                                        return;
                                    }

                                    // Check if the resident is already listed in the ranking table
                                    fetch(`check_resident_ranking.php?id=${resident.id}&calamity=${encodeURIComponent(calamity)}`)
                                        .then(response => response.json())
                                        .then(isListed => {
                                            if (isListed) {
                                                alert('This resident is already listed for the selected calamity.');
                                            } else {
                                                // Populate fields if not already listed
                                                residentInput.value = `${resident.firstname} ${resident.middlename} ${resident.lastname} ${resident.suffix}`;
                                                purokInput.value = resident.purok;
                                                suggestionsDiv.innerHTML = ''; // Clear suggestions
                                            }
                                        })
                                        .catch(error => console.error('Error checking resident ranking:', error));
                                });

                                suggestionsDiv.appendChild(suggestion);
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching residents:', error));
            });

            // Close suggestions when clicking outside
            document.addEventListener('click', function(event) {
                if (!residentInput.contains(event.target) && !suggestionsDiv.contains(event.target)) {
                    suggestionsDiv.innerHTML = '';
                }
            });
        });
    </script>
</body>
<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Are you sure you want to logout?</h2>
        <button id="confirm-logout" class="btn">Yes</button>
        <button id="cancel-logout" class="btn">No</button>
    </div>
</div>

</html>