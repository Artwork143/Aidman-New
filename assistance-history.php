<?php include 'check_admin.php';
require 'db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistance Scheduling</title>
    <link rel="stylesheet" href="assistance.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .hidden {
            display: none;
        }


        .table-header {
            display: flex;
            justify-content: space-between;
        }

        .table-header h3 {
            font-size: 1.5rem;
            color: #555;
            text-align: center;
            margin-bottom: 20px;
            margin-top: 0;
            text-align: left;
        }

        /* Dropdown structure */
        .arrow-dropdown {
            position: relative;
        }

        .arrow-dropdown-toggle {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .arrow-dropdown-content {
            display: none;
            /* Hidden by default */
            position: absolute;
            left: 0;
            top: 100%;
            background-color: #78B3CE;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            min-width: 160px;
            z-index: 1;
        }

        .arrow-toggle {
            margin-left: auto;
        }
    </style>
</head>

<body>
    <div class="container" id="pageContent">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.jpg" alt="Barangay Logo">
                <h1>AIDMAN</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
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
                    <li><a href="event-control-system.php"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Event Control System</a></li>
                    <!-- Assistance Scheduling Dropdown (onclick) -->
                    <li class="arrow-dropdown" id="assistance-scheduling-dropdown">
                        <div class="arrow-dropdown-toggle" onclick="toggleDropdown('assistance-scheduling-dropdown')">
                            <a href="assistance-scheduling.php" style="flex-grow: 1;"><i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling</a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>

                    </li>
                    <li class="active" >
                        <a href="assistance-history.php" style="font-size: 0.85em" class="nav-item active"><i class="fa-solid fa-history"></i> Assistance History</a>
                    </li>
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

                    <!-- Profile Dropdown -->
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
            <section class="assistance-history-content">
    <div class="table-wrapper">
        <div class="table-header">
            <h3>Assistance History</h3>
            <input type="text" id="searchBar" class="search-container" placeholder="Search Assistance History">
        
        </div>

        <!-- Search Bar -->
        

        <!-- Assistance History Table -->
        <table id="assistanceHistoryTable" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Distribution Date/Time</th>
                    <th>Status</th>
                    <th>Received Supplies</th>
                </tr>
            </thead>
            <tbody id="assistanceHistoryTableBody">
                <?php
                require_once 'db_connect.php';

                // Fetch data from schedule_residents, scheduled_assistance, and scheduled_assistance_items for only 'received' status
                $sql = "
                SELECT 
                    sr.fullname,
                    sa.pickup_date AS distribution_datetime,
                    CASE
                        WHEN sr.assistance_status = 'received' THEN 'Received'
                        ELSE NULL
                    END AS assistance_status,
                    GROUP_CONCAT(CONCAT(sai.quantity, ' ', i.unit, ' of ', i.name) SEPARATOR ', ') AS received_items
                FROM schedule_residents sr
                LEFT JOIN scheduled_assistance sa ON sr.id = sa.resident_id
                LEFT JOIN scheduled_assistance_items sai ON sa.id = sai.schedule_id
                LEFT JOIN inventory i ON sai.item_id = i.id
                WHERE sr.assistance_status = 'received'  -- Filter only 'received' status
                GROUP BY sr.id, sa.pickup_date, sr.assistance_status
                ORDER BY sa.pickup_date DESC
                ";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                        echo "<td>" . (!empty($row['distribution_datetime']) ? htmlspecialchars($row['distribution_datetime']) : 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['assistance_status']) . "</td>";
                        echo "<td class='received-items'>" . (!empty($row['received_items']) ? htmlspecialchars($row['received_items']) : 'No supplies received') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='no-record'>No assistance history found for received status.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div id="paginationControls"></div>
    </div>
</section>

        </main>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
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

        document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("assistanceHistoryTable");
    const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    const searchBar = document.getElementById("searchBar");
    const paginationControls = document.getElementById("paginationControls");
    const rowsPerPage = 5;  // Change this number to adjust the number of rows per page
    let currentPage = 1;

    // Pagination Functionality
    function displayPage(page) {
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = page * rowsPerPage;

        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = i >= startIndex && i < endIndex ? "" : "none";
        }

        // Update pagination controls
        updatePaginationControls();
    }

    function updatePaginationControls() {
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        paginationControls.innerHTML = "";  // Clear existing controls

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement("button");
            pageButton.textContent = i;
            pageButton.className = i === currentPage ? "active" : "";  // Add active class to the current page
            pageButton.addEventListener("click", function () {
                currentPage = i;
                displayPage(i);
            });
            paginationControls.appendChild(pageButton);
        }
    }

    // Search Functionality
    searchBar.addEventListener("input", function () {
        const filter = searchBar.value.toLowerCase();
        let visibleRowCount = 0;

        for (let i = 0; i < rows.length; i++) {
            const rowText = rows[i].textContent.toLowerCase();
            if (rowText.includes(filter)) {
                rows[i].style.display = "";  // Show the row
                visibleRowCount++;
            } else {
                rows[i].style.display = "none";  // Hide the row
            }
        }

        // Update pagination after filtering
        if (filter === "") {
            currentPage = 1;  // Reset to page 1 if search is cleared
            displayPage(1);
        } else {
            currentPage = 1;  // Reset to page 1 after filtering
            updatePaginationControls();
        }
    });

    // Initial Setup: Display the first page
    displayPage(currentPage);
});
        </script>
</body>

</html>