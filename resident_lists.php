<?php include 'check_admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css">
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="font-awesome/css/all.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Custom SweetAlert Style Override */
        .swal2-popup {
            border-radius: 10px;
            font-size: 16px;
            padding: 20px;
        }
        .swal2-title {
            font-size: 20px;
            font-weight: bold;
        }
        .swal2-confirm, .swal2-cancel {
            border-radius: 5px;
            padding: 10px 20px;
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
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li><a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
                    <li><a href="resident_lists.php"><i class="fas fa-warehouse"></i> Resident Information</a></li>
                    <li><a href="account_control.php"><i class="fas fa-user-cog"></i> Account Control</a></li>
                    <li><a href="event-control-system.php"><i class="fas fa-calendar-alt"></i> Event Control</a></li>
                    <li><a href="assistance-scheduling.php"><i class="fas fa-calendar-check"></i> Assistance Scheduling</a></li>
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
                            <a href="account-information.php" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Account Info</span>
                            </a>
                            <a href="./email-inbox.html" class="dropdown-item">
                                <i class="fas fa-envelope-open"></i>
                                <span>Inbox</span>
                            </a>
                            <a href="login.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>                            
                        </div>
                    </div>
                </div>
            </header>
            
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "aidman-db";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Pagination variables
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $page = max($page, 1);
            $offset = ($page - 1) * $limit;

            // Search functionality
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $searchQuery = "";
            if (!empty($search)) {
                $searchQuery = " WHERE firstname LIKE ? OR lastname LIKE ?";
            }

            $stmt = $conn->prepare("SELECT id, firstname, middlename, lastname, suffix, gender, age, marital_status, purok FROM resident_list" . $searchQuery . " LIMIT ?, ?");
            if (!empty($search)) {
                $searchParam = "%" . $search . "%";
                $stmt->bind_param("ssii", $searchParam, $searchParam, $offset, $limit);
            } else {
                $stmt->bind_param("ii", $offset, $limit);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            $countStmt = $conn->prepare("SELECT COUNT(*) AS count FROM resident_list" . $searchQuery);
            if (!empty($search)) {
                $countStmt->bind_param("ss", $searchParam, $searchParam);
            }
            $countStmt->execute();
            $totalResult = $countStmt->get_result();
            $totalRow = $totalResult->fetch_assoc();
            $totalUsers = $totalRow['count'];
            $totalPages = ceil($totalUsers / $limit);
            ?>

            <h2>Resident List</h2>
            <div id="account-manage">
                <div class="table-controls">
                    <!-- Add Resident Button -->
                    <a href="add_residentlist.php" class="add-resident-button">+ Add Resident</a>

                    <!-- Search Bar -->
                    <form action="resident_lists.php" method="GET" id="residentlist-search-bar">
                        <input type="text" name="search" placeholder="Search Resident Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($search) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th> <!-- Always show this column -->
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Marital Status</th>
                        <th>Purok</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                                <tbody>
                    <?php
                    $count = $offset + 1;
                    if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="10" style="text-align: center;">No results found for '<?php echo htmlspecialchars($search); ?>'</td>
                        </tr>
                    <?php endif;
                    while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="#"><?php echo $count++; ?></td>
                            <td data-label="First Name"><?php echo htmlspecialchars($user['firstname']); ?></td>
                            <td data-label="Middle Name"><?php echo htmlspecialchars($user['middlename']); ?></td>
                            <td data-label="Last Name"><?php echo htmlspecialchars($user['lastname']); ?></td>
                            <td data-label="Suffix" style="color: <?php echo empty($user['suffix']) ? 'grey' : 'black'; ?>">
                                <?php echo empty($user['suffix']) ? 'None' : htmlspecialchars($user['suffix']); ?>
                            </td>
                            <td data-label="Gender"><?php echo htmlspecialchars($user['gender']); ?></td>
                            <td data-label="Age"><?php echo htmlspecialchars($user['age']); ?></td>
                            <td data-label="Marital Status"><?php echo htmlspecialchars($user['marital_status']); ?></td>
                            <td data-label="Purok"><?php echo htmlspecialchars($user['purok']); ?></td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="#" class="edit-button" onclick="confirmEdit(<?php echo $user['id']; ?>)">Edit</a>
                                    <a href="javascript:void(0);" class="delete-button" onclick="confirmDelete(<?php echo $user['id']; ?>)">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

                </table>

                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="resident_lists.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="prev">&laquo; Prev</a>
                    <?php endif; ?>
                    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="resident_lists.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="next">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- SweetAlert2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        // Confirm Edit Action
        function confirmEdit(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to edit this resident record.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, edit it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'edit_resident.php?id=' + id;
                }
            });
        }

        function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to delete this resident record.',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to the delete script with the resident ID
            window.location.href = 'delete_resident.php?id=' + id;
        }
    });
}

    </script>

</body>
</html>
