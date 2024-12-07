
<?php include 'check_admin.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="font-awesome/css/all.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li><a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
                    <li class="arrow-dropdown">
                        <div class="arrow-dropdown-toggle" id="account-control-link">
                            <a href="account_control.php" style="flex-grow: 1;">
                                <i class="fas fa-user-cog mr-2"></i> Account Control Panel Register
                            </a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>
                        <div class="arrow-dropdown-content" id="dropdown-content">
                            <a href="account-management.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'account-management.php' ? 'active' : ''; ?>" id="account-management-link">
                                <i class="fa-solid fa-file-invoice"></i> Account Management
                            </a>
                        </div>
                    </li>
                    <li><a href="event-control-system.php"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Event Control System</a></li>
                    <li><a href="assistance-scheduling.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <header>
                <h2>Administrator</h2>
                <div class="header-right">
                    <i class="fas fa-bell"></i>
                    <div class="dropdown">
                        <i class="fas fa-user-circle"></i>
                        <div class="dropdown-menu">
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
            // Connect to the database
            $servername = "localhost";
            $username = "root"; // your MySQL username
            $password = ""; // your MySQL password
            $dbname = "aidman-db";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Pagination variables
            $limit = 10; // Number of users per page
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page
            $page = max($page, 1); // Ensure page is at least 1
            $offset = ($page - 1) * $limit; // Calculate the offset

            // Fetch users with limit and offset
            $stmt = $conn->prepare("SELECT id, fullname, username, email, role FROM users LIMIT ?, ?");
            $stmt->bind_param("ii", $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            // Count total users for pagination
            $totalResult = $conn->query("SELECT COUNT(*) AS count FROM users");
            $totalRow = $totalResult->fetch_assoc();
            $totalUsers = $totalRow['count'];
            $totalPages = ceil($totalUsers / $limit); // Total pages calculation
            ?>

            <h2>Registered Users</h2>
            <div id="account-manage">
                <table>
                    <thead>
                        <tr>
                            <th>Numbers</th> 
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Password</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = $offset + 1; // Start counting from the current offset + 1
                        while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="#"><?php echo $count++; ?></td> <!-- Display the number -->
                            <td data-label="Fullname"><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td data-label="Username"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td data-label="Role"><?php echo htmlspecialchars($user['role']); ?></td>
                            <td data-label="Password">
                                <span class="password-placeholder">Protected</span>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                <a href="#" class="edit-button" onclick="confirmEdit(<?php echo $user['id']; ?>)">Edit</a>
                                    <a href="#" class="delete-button" onclick="confirmDelete(<?php echo $user['id']; ?>)">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="prev-button">&lt; Previous</a>
                    <?php endif; ?>
                    
                    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="next-button">Next &gt;</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            // Close connection
            $stmt->close();
            $conn->close();
            ?>
        </main>
    </div>

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    function confirmDelete(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete this user!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to delete page
                window.location.href = 'account-manage-delete.php?id=' + userId;
            }
        });
    }

    function confirmEdit(userId) {
    console.log("Editing user with ID:", userId); // Debug log
    Swal.fire({
        title: 'Confirm Edit',
        text: "Are you sure you want to edit this user?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to the edit page
            window.location.href = 'account-manage-edit.php?id=' + userId;
        }
    });
}


    </script>

    <script src="js/admin-dashboard.js"></script>
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
