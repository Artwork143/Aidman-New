<?php include 'official_check.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Link to SweetAlert -->
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.jpg" alt="Barangay Logo">
                <h1>AIDMAN</h1>
                <p></p>
            </div>
            <nav>
            <ul>
                    <li><a href="official-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="official-aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li><a href="official-inven-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
                    <li><a href="official-assistance-sched.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling</a></li>

                    
                </ul>
        </aside>
        <main>
            <header>
                <h2>Official</h2>
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
                                    <div class="dropdown-item" onclick="location.href='official-inven-dashboard.php';" style="cursor: pointer;">
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
            <!-- Container for card headers -->
            <div id="unique-card-container" class="card-container">
                <!-- Left card header -->
                <div class="card-header card-header-left">
                    <h3>Resident Registration Form</h3>
                    <form action="official-submit-aid.php" method="post">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required><br>

                        <label for="damage_severity">Damage Severity (1-10):</label>
                        <input type="number" id="damage_severity" name="damage_severity" min="1" max="10" required><br>

                        <label for="number_of_occupants">Number of Occupants:</label>
                        <input type="number" id="number_of_occupants" name="number_of_occupants" required><br>

                        <label for="vulnerability">Vulnerability (1-10):</label>
                        <input type="number" id="vulnerability" name="vulnerability" min="1" max="10" required><br>

                        <label for="income_level">Income Level (1-10):</label>
                        <input type="number" id="income_level" name="income_level" min="1" max="10" required><br>

                        <label for="special_needs">Special Needs (1-10):</label>
                        <input type="number" id="special_needs" name="special_needs" min="1" max="10" required><br>

                        <input type="submit" value="Submit">
                    </form>
                </div>

                <!-- Right card header -->
                <div class="card-header card-header-right">
                    <h3>Leaderboard</h3>
                    <button onclick="printLeaderboard()" class="btn-print-leaderboard" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.3s, box-shadow 0.3s, filter 0.3s;">Print Leaderboard</button>
                    <style>
                        .btn-print-leaderboard:hover {
                            background-color: #45a049;
                            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                            filter: brightness(1.2);
                        }
                    </style>
                    <style>
                        .btn-print-leaderboard:hover {
                            background-color: #45a049;
                            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                            animation: shine 1s ease-in-out;
                        }

                        @keyframes shine {
                            0% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }

                            50% {
                                box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
                            }

                            100% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }
                        }
                    </style>
                    <style>
                        .btn-print-leaderboard:hover {
                            background-color: #45a049;
                            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                            animation: shine 1s ease-in-out;
                        }

                        @keyframes shine {
                            0% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }

                            50% {
                                box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
                            }

                            100% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }
                        }
                    </style>
                    <ul class="leaderboard" id="leaderboard">
                        <?php
                        // Example PHP to fetch data from the database
                        $pdo = new PDO('mysql:host=localhost;dbname=aidman-db', 'root', '');
                        $stmt = $pdo->query('SELECT id, name, total_score FROM residents ORDER BY total_score DESC');
                        $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php foreach ($rankings as $index => $resident): ?>
                            <li class="person">
                                <span class="icon"><?php echo $index + 1; ?></span>
                                <span class="nickname"><?php echo htmlspecialchars($resident['name']); ?></span>
                                <span class="score"><?php echo htmlspecialchars($resident['total_score']); ?></span>
                                <a href="official-edit-data.php" class="btn btn-leaderboard-edit edit-btn" official-data-id="<?php echo urlencode($resident['id']); ?>">Edit</a>
                                <a href="#" class="btn btn-leaderboard-delete delete-btn" official-data-id="<?php echo urlencode($resident['id']); ?>">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
    <script src="js/official-dashboard.js"></script>
    <script>
        function printLeaderboard() {
            // Open a new window to print leaderboard
            const leaderboard = document.getElementById('leaderboard').cloneNode(true);
            const editButtons = leaderboard.querySelectorAll('.edit-btn, .delete-btn');
            editButtons.forEach(button => button.remove());
            leaderboard.style.fontFamily = 'Arial, sans-serif';
            leaderboard.style.width = '100%';
            leaderboard.style.margin = '0 auto';
            leaderboard.style.borderCollapse = 'collapse';
            const rows = leaderboard.querySelectorAll('li');
            rows.forEach((row, index) => {
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.padding = '10px';
                row.style.borderBottom = '1px solid #ccc';
                if (index % 2 === 0) {
                    row.style.backgroundColor = '#f9f9f9';
                }
                row.style.fontSize = '16px';
            });
            const printContents = leaderboard.outerHTML;
            const originalContents = document.body.innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print Leaderboard</title>');
            printWindow.document.write('<style>body { font-family: Arial, sans-serif; margin: 20px; } h2 { text-align: center; font-size: 26px; margin-bottom: 10px; } h3 { text-align: center; font-size: 24px; margin-bottom: 20px; } .leaderboard { list-style-type: none; padding: 0; margin: 0; width: 100%; } .leaderboard li { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ccc; } .leaderboard li:nth-child(even) { background-color: #f9f9f9; } .icon { font-weight: bold; } .nickname, .score { font-size: 16px; }</style>');
            printWindow.document.write('<style>body { font-family: Arial, sans-serif; margin: 20px; } h3 { text-align: center; font-size: 24px; margin-bottom: 20px; } .leaderboard { list-style-type: none; padding: 0; margin: 0; width: 100%; } .leaderboard li { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ccc; } .leaderboard li:nth-child(even) { background-color: #f9f9f9; } .icon { font-weight: bold; } .nickname, .score { font-size: 16px; }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>PRIORITY RANK LIST BASE ON CRITERIA</h2>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }


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