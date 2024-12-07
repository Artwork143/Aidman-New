<?php include 'official_check.php'; ?>


<?php 
// Include database connection
include 'db_connect.php';

// Check if the connection is successful
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Verify if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in. Please log in first.");
}

// Fetch images from the database
$sql = "SELECT * FROM images"; // Assuming you have an 'images' table
$result = $conn->query($sql);
$images = [];

if ($result && $result->num_rows > 0) {
    // Fetch all images
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path']; // Assuming image_path is a column in your images table
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            </nav>
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
            <section class="event-control-carousel-container">
                <button class="event-control-arrow-btn left" onclick="previousSlide()">&#10094;</button>
                <div class="event-control-carousel" id="carousel">
                    <?php foreach ($images as $index => $image): ?>
                        <img src="<?php echo $image; ?>" alt="Event <?php echo $index + 1; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                    <?php endforeach; ?>
                </div>
                <button class="event-control-arrow-btn right" onclick="nextSlide()">&#10095;</button>
            </section>
        </main>

        

    </div>
    <script src="js/official-dashboard.js"></script>
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
</html>
