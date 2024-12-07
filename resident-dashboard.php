<?php include 'check_resident.php'; ?>


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

// Fetch the logged-in user's ID
$resident_id = $_SESSION['user_id'];

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

// Fetch notifications for the logged-in resident from the `scheduled_assistance` table
// Fetch notifications for the logged-in resident
$notifications_sql = "
    SELECT 
        sr.id AS resident_id, 
        sr.fullname, 
        sr.assistance_status, 
        sa.notification_message 
    FROM 
        schedule_residents sr
    JOIN 
        scheduled_assistance sa 
    ON 
        sr.id = sa.resident_id
    WHERE 
        sr.id = ? 
        AND sr.assistance_status NOT IN ('pickup', 'received')
";

// Prepare the statement and check for errors
if (!$stmt = $conn->prepare($notifications_sql)) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $resident_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row; // Collect notifications
    }
}

// Check if there are pending notifications
$has_new_notifications = count($notifications) > 0;

// Close the statement
$stmt->close();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assistance.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.css">
    <!-- Link to Font Awesome for icons -->
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

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5);
        }

        /* Modal Container */
        .modal-container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            top: 50%;
            /* Align to center vertically */
            left: 50%;
            /* Align to center horizontally */
            transform: translate(-50%, -50%);
            /* Shift the modal back by 50% of its own height/width */
            z-index: 1050;
            width: 100%;
            max-width: 28rem;
            margin: auto;
            padding: 1.5rem;
            position: fixed;
        }

        /* Close Button */
        .modal-close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: red;
            border: none;
            font-size: 1.25rem;
            color: white;
            /* Gray */
            cursor: pointer;
            transition: color 0.3s ease;

        }

        .modal-close-btn:hover {
            background-color: firebrick;
            /* Darker Gray */
        }

        /* Modal Header */
        .modal-header {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            /* Gray-800 */
            margin-bottom: 1rem;
        }

        /* Modal Content */
        .modal-content {
            margin-bottom: 1.5rem;
        }

        .modal-label {
            font-weight: bold;
            color: #4b5563;
            /* Gray-700 */
        }

        .modal-value {
            color: #6b7280;
            /* Gray-600 */
        }

        .modal-supplies-title {
            font-weight: 500;
            color: #4b5563;
            /* Gray-700 */
            margin-top: 1rem;
        }

        .modal-supply-list {
            list-style-type: disc;
            padding-left: 1.5rem;
            color: #6b7280;
            /* Gray-600 */
        }

        /* Modal Actions */
        .modal-actions {
            text-align: center;
            margin-top: 1.5rem;
        }

        .modal-print-btn {
            display: block;
            width: 100%;
            background-color: #3b82f6;
            /* Blue-500 */
            color: #ffffff;
            padding: 0.5rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-print-btn:hover {
            background-color: #2563eb;
            /* Blue-600 */
        }

        .modal-note {
            font-size: 0.875rem;
            color: #9ca3af;
            /* Gray-500 */
            margin-top: 0.5rem;
        }

        .hidden {
            display: none;
        }
    </style>
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
                    <li><a href="resident-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <header>
                <h2>Resident</h2>
                <div class="header-right">
                    <!-- Notification Bell -->
                    <div class="notification-bell" id="notification-bell">
                        <i class="fas fa-bell" style="color: <?php echo $has_new_notifications ? 'red' : '#555'; ?>;"></i>
                        <?php if ($has_new_notifications): ?>
                            <span class="badge"><?php echo count($notifications); ?></span>
                        <?php endif; ?>
                        <div class="dropdown-menu notifications" id="notification-dropdown">
                            <?php if ($has_new_notifications): ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="dropdown-item" data-id="<?php echo $notification['resident_id']; ?>">
                                        <p>
                                            <strong>Status:</strong> <?php echo htmlspecialchars($notification['assistance_status']); ?><br>
                                            <?php echo htmlspecialchars($notification['notification_message']); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <div class="dropdown-item">
                                    <p>No new notifications</p>
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

        <!-- Logout Confirmation Modal -->
        <div id="logout-modal" class="logout-modal">
            <div class="logout-content">
                <span class="close">&times;</span>
                <h2>Are you sure you want to logout?</h2>
                <button id="confirm-logout" class="btn">Yes</button>
                <button id="cancel-logout" class="btn">No</button>
            </div>
        </div>

        <!-- Notification Details Modal -->
        <div id="notification-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" id="close-assistance">&times;</span>
                <h2>Assistance Details</h2>
                <p><strong>Scheduled Pickup Date:</strong> <span id="pickup-date"></span></p>
                <p><strong>Supplies:</strong></p>
                <ul id="supply-list"></ul>
                <button id="confirm-assistance" class="btn">Confirm Assistance</button>
                <button id="cancel-assistance" class="btn">Cancel</button>
            </div>
        </div>




    </div>
    <!-- Receipt Modal -->
    <div id="receipt-modal" class="modal-overlay hidden">
        <div class="modal-container">
            <!-- Close Button -->
            <button id="close-receipt-modal" class="modal-close-btn">&times;</button>

            <!-- Modal Header -->
            <h2 class="modal-header">Receipt Details</h2>

            <!-- Modal Content -->
            <div id="receipt-content" class="modal-content">
                <p>
                    <strong class="modal-label">Resident Name:</strong>
                    <span id="resident-name" class="modal-value"></span>
                </p>
                <p>
                    <strong class="modal-label">Scheduled Pickup Date:</strong>
                    <span id="receipt-pickup-date" class="modal-value"></span>
                </p>
                <p class="modal-supplies-title">Supplies:</p>
                <ul id="receipt-supply-list" class="modal-supply-list"></ul>
            </div>

            <!-- Modal Actions -->
            <div class="modal-actions">
                <button id="print-receipt" class="modal-print-btn">Print Receipt</button>
                <p class="modal-note">Or take a screenshot for your records.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.js"></script>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Notification items
            const notificationItems = document.querySelectorAll('.dropdown-item[data-id]');
            const receiptModal = document.getElementById('receipt-modal');
            const closeReceiptModal = document.getElementById('close-receipt-modal');
            const printReceipt = document.getElementById('print-receipt');

            const residentNameElem = document.getElementById('resident-name');
            const receiptPickupDateElem = document.getElementById('receipt-pickup-date');
            const receiptSupplyListElem = document.getElementById('receipt-supply-list');

            notificationItems.forEach((item) => {
                item.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');

                    if (!notificationId) {
                        alert('Notification ID is missing.');
                        return;
                    }

                    // Fetch details for the selected notification
                    fetch('fetch-assistance-details.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `notification_id=${encodeURIComponent(notificationId)}`,
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.status === 'success') {
                                // Populate modal with data
                                residentNameElem.textContent = data.resident_fullname || 'N/A';
                                receiptPickupDateElem.textContent = data.pickup_date || 'N/A';

                                // Populate supplies
                                receiptSupplyListElem.innerHTML = '';
                                data.supplies.forEach((supply) => {
                                    const listItem = document.createElement('li');
                                    listItem.textContent = `${supply.quantity} ${supply.unit} of ${supply.item_name}`;
                                    receiptSupplyListElem.appendChild(listItem);
                                });

                                // Show modal
                                receiptModal.style.display = 'block';
                            } else {
                                alert(data.message || 'Failed to fetch details.');
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            alert('An error occurred while fetching details.');
                        });
                });
            });

            // Close modal
            closeReceiptModal.addEventListener('click', () => {
                receiptModal.style.display = 'none';
            });

            // Print receipt
            printReceipt.addEventListener('click', () => {
                const printContent = document.getElementById('receipt-content').innerHTML;
                const newWindow = window.open('', '', 'width=600,height=400');
                newWindow.document.write('<html><head><title>Print Receipt</title></head><body>');
                newWindow.document.write(printContent);
                newWindow.document.write('</body></html>');
                newWindow.document.close();
                newWindow.print();
            });

            // Close modal when clicking outside of it
            window.addEventListener('click', (event) => {
                if (event.target === receiptModal) {
                    receiptModal.style.display = 'none';
                }
            });
        });
    </script>

    <script src="js/resident-dashboard.js"></script>
</body>

</html>