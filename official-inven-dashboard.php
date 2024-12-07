<?php include 'official_check.php'; ?>


<?php
// Database Connection (db_connect.php)
$servername = "localhost";
$username = "root";
$password = "";
$database = "aidman-db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .inventory-container {
            padding: 15px;
        }

        .inventory-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-start;
        }

        .inven-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }


        .inventory-item {
            display: flex;
            margin-bottom: 20px;
        }

        .inventory-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .inventory-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .inventory-details {
            flex: 1;
        }

        .alert {
            margin-top: 10px;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #721c24;
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
    </style>
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
    <link rel="stylesheet" href="css/inventory-modal.css"> <!-- Added inventory modal CSS link -->
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

            <div class="inventory-container">
                <div class="inventory-header">
                
                </div>
                <div class="inventory-list" id="inven-inventory-list">
                    <?php
                    // Fetch inventory items from the database
                    require 'db_connect.php';
                    $sql = "SELECT id, name, quantity, threshold_quantity, unit, image_path 
                FROM inventory 
                ORDER BY quantity ASC"; // Order by quantity to display low-stock items first
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Display inventory item
                            echo '<div class="inventory-item">';
                            echo '<div class="inventory-card">';
                            if ($row['image_path']) {
                                echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['name']) . '" class="inventory-image">';
                            }
                            echo '<div class="inventory-details">';
                            echo '<h4 class="inventory-name">' . htmlspecialchars($row['name']) . '</h4>';
                            echo '<p class="inventory-info"><strong>Quantity:</strong> ' . htmlspecialchars($row['quantity']) . ' ' . htmlspecialchars($row['unit']) . '</p>';
                            echo '<div class="inventory-actions">';
                            echo '</div>'; // .inventory-actions

                            // Add alert for low stock directly inside the card
                            if ($row['quantity'] <= $row['threshold_quantity']) {
                                echo '<div class="alert alert-warning alert-dismissible">';
                                echo htmlspecialchars($row['name']) . ' is low on stock. Please restock.';
                                echo '</div>';
                            }

                            echo '</div>'; // .inventory-details
                            echo '</div>'; // .inventory-card
                            echo '</div>'; // .inventory-item
                        }
                    } else {
                        echo '<p>No inventory items found.</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Add Supplies Modal -->
            <div id="inven-add-supplies-modal" class="inven-modal">
                <div class="inven-modal-content inven-realistic-modal">
                    <span class="inven-close">&times;</span>
                    <h2>Add Supplies</h2>
                    <form id="inven-add-supplies-form" action="add_supply.php" method="POST" enctype="multipart/form-data">
                        <label for="inven-supply-name">Name:</label>
                        <input type="text" id="inven-supply-name" name="supply_name" required>

                        <label for="inven-supply-quantity">Quantity:</label>
                        <input type="number" id="inven-supply-quantity" name="supply_quantity" required>

                        <label for="inven-supply-unit">Unit:</label>
                        <select id="inven-supply-unit" name="supply_unit">
                            <option value="kg">Kilo</option>
                            <option value="amount">Amount</option>
                        </select>

                        <label for="inven-expiry-date">Expiry Date:</label>
                        <input type="date" id="inven-expiry-date" name="expiry_date" required>

                        <label for="inven-supply-image">Upload Image:</label>
                        <input type="file" id="inven-supply-image" name="supply_image" accept="image/*">

                        <button type="submit" class="inven-btn">Save</button>
                    </form>
                </div>
            </div>

            <!-- Edit Supplies Modal -->
            <div id="inven-edit-supplies-modal" class="inven-modal">
                <div class="inven-modal-content inven-realistic-modal">
                    <span class="inven-close-edit">&times;</span>
                    <h2>Edit Supplies</h2>
                    <form id="inven-edit-supplies-form" action="edit_supply.php" method="POST">
                        <input type="hidden" id="edit-supply-id" name="edit_supply_id">
                        <label for="edit-supply-name">Name:</label>
                        <input type="text" id="edit-supply-name" name="edit_supply_name" required>

                        <label for="edit-supply-quantity">Quantity:</label>
                        <input type="number" id="edit-supply-quantity" name="edit_supply_quantity" required>

                        <label for="edit-expiry-date">Expiry Date:</label>
                        <input type="date" id="edit-expiry-date" name="edit_expiry_date" required>

                        <button type="submit" class="inven-btn"> Update</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="inven-delete-supply">
        <div class="inven-delete-content">
            <span class="inven-close-delete">&times;</span>
            <h2>Delete Supply</h2>
            <p>Are you sure you want to delete this supply?</p>
            <form id="delete-supply-form" action="delete_supply.php" method="GET">
                <input type="hidden" id="delete-supply-id" name="id">
                <button type="submit" class="delete-confirm-btn">Yes, Delete</button>
                <button type="button" class="delete-cancel-btn" class="delete-cancel-btn">Cancel</button>
            </form>
        </div>
    </div>


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

        
        // Modal Script
        const addSuppliesBtn = document.getElementById('inven-add-supplies-btn');
        const addSuppliesModal = document.getElementById('inven-add-supplies-modal');
        const closeModal = document.querySelector('.inven-close');
        const addSuppliesForm = document.getElementById('inven-add-supplies-form');
        const inventoryList = document.getElementById('inven-inventory-list');

        const editSuppliesModal = document.getElementById('inven-edit-supplies-modal');
        const closeModalEdit = document.querySelector('.inven-close-edit');
        const editSuppliesForm = document.getElementById('inven-edit-supplies-form');

        addSuppliesBtn.addEventListener('click', () => {
            addSuppliesModal.style.display = 'block';
        });

        closeModal.addEventListener('click', () => {
            addSuppliesModal.style.display = 'none';
        });

        closeModalEdit.addEventListener('click', () => {
            editSuppliesModal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target == addSuppliesModal) {
                addSuppliesModal.style.display = 'none';
            }
            if (event.target == editSuppliesModal) {
                editSuppliesModal.style.display = 'none';
            }
        });

        // Form submission handler for adding supplies
        addSuppliesForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent form from submitting normally

            const formData = new FormData(addSuppliesForm);

            fetch('add_supply.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire('Success', 'Supply added successfully!', 'success').then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                });
        });

        // Event listener for handling edit and delete functionality
        document.addEventListener('click', (event) => {
            if (event.target.classList.contains('edit-supply-btn')) {
                const supplyId = event.target.getAttribute('data-id');
                const supplyName = event.target.closest('.inventory-item').querySelector('.inventory-name').textContent;
                const supplyQuantity = event.target.closest('.inventory-item').querySelector('.inventory-info').textContent.match(/\d+/)[0];
                const expiryDate = event.target.closest('.inventory-item').querySelector('.inventory-info').getAttribute('data-expiry-date');

                let formattedDate = "";
                if (expiryDate && expiryDate !== '0000-00-00') {
                    formattedDate = expiryDate;
                }

                document.getElementById('edit-supply-id').value = supplyId;
                document.getElementById('edit-supply-name').value = supplyName;
                document.getElementById('edit-supply-quantity').value = supplyQuantity;
                document.getElementById('edit-expiry-date').value = formattedDate;

                // Show the modal for editing supplies
                document.getElementById('inven-edit-supplies-modal').style.display = 'block';
            }

            // Check if the clicked element has the class 'delete-btn' (Delete button)
            if (event.target.classList.contains('delete-supply-btn')) {
                // Fetch supply ID from the data-id attribute of the clicked delete button
                const supplyId = event.target.getAttribute('data-id');

                // Sweet Alert confirmation before deletion
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `delete_supply.php?id=${supplyId}`;
                    }
                });
            }
        });

        // Form submission handler for editing supplies
        editSuppliesForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const formData = new FormData(editSuppliesForm);

            fetch('edit_supply.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire('Success', 'Supply updated successfully!', 'success').then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
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

<?php
// add_supply.php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['supply_name'];
    $quantity = $_POST['supply_quantity'];
    $unit = $_POST['supply_unit'];
    $expiry_date = $_POST['expiry_date'];

    // Handling image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["supply_image"]["name"]);
    move_uploaded_file($_FILES["supply_image"]["tmp_name"], $target_file);

    // Insert into database
    $sql = "INSERT INTO inventory (name, quantity, unit, expiry_date, image_path) VALUES ('$name', '$quantity', '$unit', '$expiry_date', '$target_file')";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
}
?>

<?php
// edit_supply.php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_supply_id'];
    $name = $_POST['edit_supply_name'];
    $quantity = $_POST['edit_supply_quantity'];
    $expiry_date = $_POST['edit_expiry_date'];

    // Update database
    $sql = "UPDATE inventory SET name='$name', quantity='$quantity', expiry_date='$expiry_date' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
}
?>

<?php
// delete_supply.php
require 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete from database
    $sql = "DELETE FROM inventory WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>window.location.href = 'admin-dashboard.php?status=delete';</script>";
    } else {
        echo "<script>window.location.href = 'admin-dashboard.php?status=error';</script>";
    }

    $conn->close();
}
?>