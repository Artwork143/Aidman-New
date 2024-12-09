<?php include 'check_admin.php'; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assistance.css">
    <link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" href="font-awesome/css/all.min.css">
    <!-- Link to Font Awesome for icons -->
    <style>
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

        .dropdown-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #calamity-type-dropdown {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Style the 'Add Calamity' link */
        .add-calamity-link {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .add-calamity-link:hover {
            background-color: #0056b3;
        }

        section.table-wrapper table thead th {
            background-color: #3c4a58;
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-size: 1rem;
            border-right: 1px solid #ddd;
        }

        .leaderboard-list {
            list-style: none;
            padding: 0;
        }

        .leaderboard-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f0f0f0;
            padding: 10px 0;
        }

        .leaderboard-item:last-child {
            border-bottom: none;
        }

        .rank {
            width: 30px;
            height: 30px;
            line-height: 30px;
            background-color: #007bff;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            border-radius: 50%;
            margin-right: 15px;
        }

        .name {
            flex: 1;
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .score {
            font-size: 16px;
            font-weight: bold;
            color: #555;
            margin-right: 15px;
        }

        .edit-btn,
        .delete-btn {
            padding: 5px 10px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            text-align: center;
        }

        .edit-btn {
            background-color: #28a745;
            margin-right: 5px;
        }

        .edit-btn:hover {
            background-color: #218838;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .no-data {
            text-align: center;
            color: #999;
            font-size: 14px;
            padding: 10px 0;
        }

        div .card.leaderboard {
            background-color: #0056b3;
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
                    <li><a href="ranking_resident.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
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
                    <li class="nav-item active"><a href="calamity_list.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Calamity List</a></li>

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
            <?php
            // Database connection
            require 'db_connect.php';

            // Fetch calamity list from database
            $sql = "SELECT * FROM calamity_list ORDER BY date_occurred DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            // Group calamities by type
            $calamitiesByType = [];
            while ($row = $result->fetch_assoc()) {
                $calamitiesByType[$row['type']][] = $row;
            }
            ?>

            <div style="display: flex; gap: 10px; align-items: center; padding-bottom: 10px">
                <!-- Dropdown to Select Calamity Type -->
                <div class="dropdown-container">
                    <label for="calamity-type-dropdown">Select Calamity Type:</label>
                    <select id="calamity-type-dropdown">
                        <option value="all">All</option>
                        <?php foreach (array_keys($calamitiesByType) as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <a href="add_calamity.php" class="add-calamity-link">Add Calamity</a>
            </div>



            <!-- Dynamic Table Section -->
            <section id="dynamic-content" class="table-wrapper">
                <h3 id="dynamic-heading" style="margin-top: 20px;">All Calamities</h3>
                <div style="display: flex; gap: 10px;">
                    <!-- Main Calamity Table -->
                    <table id="calamity-table-content" border="1" cellspacing="0" cellpadding="5">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date Occurred</th>
                                <th>Severity Level</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calamitiesByType as $type => $calamities): ?>
                                <?php foreach ($calamities as $calamity): ?>
                                    <tr class="calamity-row" data-calamity-name="<?php echo htmlspecialchars($calamity['name']); ?>" data-type="<?php echo htmlspecialchars($type); ?>">
                                        <td><?php echo htmlspecialchars($calamity['name']); ?></td>
                                        <td><?php echo htmlspecialchars($calamity['date_occurred']); ?></td>
                                        <td><?php echo htmlspecialchars($calamity['severity_level']); ?></td>
                                        <td><?php echo htmlspecialchars($calamity['description']); ?></td>
                                        <td>
                                            <!-- Action Buttons -->
                                            <a href="fetch_ranking.php?calamity=<?php echo urlencode($calamity['name']); ?>" class="ranking-button" style="padding: 10px; background-color: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px; margin: 5px; text-decoration: none; display: inline-block;">Ranking</a>
                                            <button class="received-button" style="padding: 10px; background-color: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px;">Received</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </section>

        </main>
        <script src="js/admin-dashboard.js"></script>
        <script>
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

            document.addEventListener('DOMContentLoaded', () => {
                const calamityDropdown = document.getElementById('calamity-type-dropdown');
                const calamityRows = document.querySelectorAll('.calamity-row');
                const dynamicHeading = document.getElementById('dynamic-heading');
                const dynamicContent = document.getElementById('dynamic-content');

                // Filter calamities based on dropdown selection
                calamityDropdown.addEventListener('change', () => {
                    const selectedType = calamityDropdown.value;
                    dynamicHeading.textContent = selectedType === 'all' ? 'All Calamities' : `List of ${selectedType}`;

                    calamityRows.forEach(row => {
                        const rowType = row.getAttribute('data-type');
                        row.style.display = (selectedType === 'all' || rowType === selectedType) ? '' : 'none';
                    });
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