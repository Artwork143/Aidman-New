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

        .modal-search {

            /* Hidden by default */
            position: fixed;
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        .modal-search-content {
            display: flex;
            flex-direction: column;
            gap: 10px;

            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;

            padding: 20px;
            width: 400px;
            border-radius: 8px;
        }

        .modal-close {
            align-self: flex-end;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        #residentSearch {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        #residentSearchResults {
            max-height: 200px;
            overflow-y: auto;
            margin: 10px 0;
            border: 1px solid #ccc;
            padding: 10px;
        }

        #residentSearchResults div {
            padding: 5px;
            cursor: pointer;
        }

        #residentSearchResults div:hover {
            background: #f0f0f0;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
        }

        .btn-add {
            margin-right: 10px;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .btn-add:hover {
            background: #0056b3;
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

        /* Main content section */
        .assistance-history-content {
            padding: 20px;
            margin-top: 20px;
        }

        /* Table container */
        .table-wrapper {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-header h3 {
            font-size: 1.5rem;
            color: #555;
            text-align: center;
            margin-bottom: 20px;
            margin-top: 0;
            text-align: left;
        }

        /* Table header */
        .table-header-section {
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #333;
            font-weight: bold;
        }


        /* Mobile responsiveness */
        @media screen and (max-width: 768px) {

            .professional-table,
            .professional-table th,
            .professional-table td {
                font-size: 12px;
            }

            .table-wrapper {
                padding: 15px;
            }
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
                            <a href="assistance-scheduling.php" class="nav-item active" style="flex-grow: 1; margin-right:0px;"><i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling</a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>
                        <div class="arrow-dropdown-content">
                            <a href="assistance-history.php"><i class="fa-solid fa-history"></i> Assistance History</a>
                        </div>
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
            <h3>Scheduled Residents</h3>
            <button id="addResidentBtn" class="btn-add">Add Resident</button>
        </div>
        <!-- Search bar -->
        <div class="search-container">
            <input type="text" id="searchBar" placeholder="Search residents..." />
        </div>
        <!-- Paginated Table -->
        <table id="residentTable" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Distribution Date/Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="residentTableBody">
                <?php
                require_once 'db_connect.php';

                $sql = "
                    SELECT 
                        sr.id,
                        sr.fullname,
                        sa.pickup_date AS distribution_datetime,
                        COALESCE(
                            CASE
                                WHEN sr.assistance_status = 'for pickup' THEN 'For Pickup'
                                WHEN sr.assistance_status = 'received' THEN 'Received'
                                ELSE 'Eligible'
                            END,
                            'Eligible'
                        ) AS assistance_status
                    FROM schedule_residents sr
                    LEFT JOIN (
                        SELECT id, resident_id, pickup_date
                        FROM scheduled_assistance
                        WHERE id IN (
                            SELECT MAX(id) 
                            FROM scheduled_assistance 
                            GROUP BY resident_id
                        )
                    ) sa ON sr.id = sa.resident_id
                    WHERE sr.assistance_status != 'received'
                ";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                        echo "<td>" . (!empty($row['distribution_datetime']) ? htmlspecialchars($row['distribution_datetime']) : 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['assistance_status']) . "</td>";
                        echo "<td>";
                        echo "<button class='btn-edit' data-resident-id='" . htmlspecialchars($row['id']) . "' data-fullname='" . htmlspecialchars($row['fullname']) . "' data-distribution-date='" . htmlspecialchars($row['distribution_datetime']) . "' data-status='" . htmlspecialchars($row['assistance_status']) . "'>Edit</button>";
                        echo "<button class='btn-delete' data-resident-id='" . htmlspecialchars($row['id']) . "'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No residents found.</td></tr>";
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
    </div>
    <!-- Modals -->
    <!-- Schedule Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <h3>Schedule Assistance</h3>
            <form id="scheduleForm">
                <label for="distributionDate">Distribution Date/Time:</label>
                <input type="datetime-local" id="distributionDate" name="distribution_date" required>

                <h4>Supplies:</h4>
                <div id="suppliesList">
                    <!-- Supply items with quantity input will be dynamically loaded here -->
                </div>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="for pickup">For Pickup</option>
                    <option value="received">Received</option>
                </select>

                <button type="submit" class="btn-submit">Submit</button>
                <button type="button" class="btn-cancel" onclick="closeModal('scheduleModal')">Cancel</button>
            </form>
        </div>
    </div>


    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Assistance Schedule</h3>
            <form id="editForm">
                <input type="hidden" id="editResidentId" name="resident_id" />

                <label for="editFullname">Full Name:</label>
                <input type="text" id="editFullname" name="fullname" disabled />

                <label for="editDistributionDate">Distribution Date/Time:</label>
                <input type="datetime-local" id="editDistributionDate" name="distribution_date" required />

                <h4>Supplies:</h4>
                <div id="editSuppliesList">
                    <!-- Supplies will be dynamically loaded -->
                </div>

                <label for="editStatus">Status:</label>
                <select id="editStatus" name="status" required>
                    <option value="for pickup">For Pickup</option>
                    <option value="received">Received</option>
                </select>

                <button type="submit" class="btn-submit">Update</button>
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
            </form>
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this schedule?</p>
            <form id="deleteForm">
                <input type="hidden" id="deleteResidentId" name="resident_id">
                <button type="submit" class="btn-confirm">Delete</button>
                <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
            </form>
        </div>
    </div>



    <!-- Search Modal -->
    <div id="residentModal" class="modal-search hidden">
        <div class="modal-search-content">
            <span id="closeModal" class="modal-close">&times;</span>
            <h3>Select a Resident</h3>
            <input type="text" id="residentSearch" placeholder="Search by name..." />
            <div id="residentSearchResults"></div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Are you sure you want to logout?</h2>
            <button id="confirm-logout" class="btn">Yes</button>
            <button id="cancel-logout" class="btn">No</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.js"></script>
    <script src="js/admin-dashboard.js"></script>
    <script>
        // Function to toggle the dropdown menu
        function toggleDropdown(dropdownId) {
            const dropdownContent = document.querySelector(`#${dropdownId} .arrow-dropdown-content`);
            const allDropdowns = document.querySelectorAll('.arrow-dropdown .arrow-dropdown-content');

            // Close all dropdowns
            allDropdowns.forEach(function(content) {
                if (content !== dropdownContent) {
                    content.style.display = 'none';
                }
            });

            // Toggle the clicked dropdown
            dropdownContent.style.display = (dropdownContent.style.display === 'block') ? 'none' : 'block';
        }

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

            // Elements
            const modals = {
                schedule: document.getElementById('scheduleModal'),
                resident: document.getElementById('residentModal'), // Ensure this is correct
            };
            const addResidentBtn = document.getElementById('addResidentBtn');
            const residentTableBody = document.getElementById('residentTableBody');
            const pageContent = document.getElementById('pageContent');
            const residentSearch = document.getElementById('residentSearch');
            const residentSearchResults = document.getElementById('residentSearchResults');
            const scheduleForm = document.getElementById('scheduleForm');
            const suppliesList = document.getElementById('suppliesList');
            let selectedResidentId = null;
            let selectedResidentFullname = '';

            // Helpers
            const openModal = (modal) => {
                if (modal) {
                    modal.style.display = 'block';
                    pageContent?.classList.add('blur');
                }
            };

            const closeModal = (modalId) => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'none';
                    pageContent?.classList.remove('blur');
                }
            };

            // Close search modal
            const closeSearchModal = () => {
                modals.resident.style.display = 'none';
                pageContent?.classList.remove('blur');
            };

            const bindModalCloseButtons = () => {
                // Close modal when clicking on close button (x)
                document.querySelectorAll('.modal .close').forEach((btn) => {
                    btn.addEventListener('click', (event) => {
                        const modal = event.target.closest('.modal');
                        closeModal(modal.id);
                    });
                });

                // Close modal when clicking on the Cancel button (for schedule modal)
                document.querySelectorAll('.btn-cancel').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const modal = btn.closest('.modal');
                        closeModal(modal.id);
                    });
                });

                // Close the Search Modal when clicking on the close button
                const closeSearchButton = document.getElementById('closeModal');
                if (closeSearchButton) {
                    closeSearchButton.addEventListener('click', closeSearchModal);
                }
            };

            const initializeModals = () => {
                window.addEventListener('click', (event) => {
                    Object.values(modals).forEach((modal) => {
                        // if (event.target === modal) closeModal(modal.id);
                    });
                });
            };

            // Resident Search
            residentSearch?.addEventListener('input', () => {
                const query = residentSearch.value.trim();
                if (query.length >= 3) {
                    fetch(`/Brgy Zone 1/search-residents.php?q=${encodeURIComponent(query)}`)
                        .then((response) => response.json())
                        .then((data) => {
                            residentSearchResults.innerHTML = '';
                            if (data.error) {
                                const errorDiv = document.createElement('div');
                                errorDiv.textContent = data.error;
                                residentSearchResults.appendChild(errorDiv);
                            } else {
                                data.forEach((resident) => {
                                    const div = document.createElement('div');
                                    div.textContent = resident.fullname;
                                    div.dataset.residentId = resident.id;
                                    div.addEventListener('click', () => {
                                        selectedResidentId = resident.id;
                                        selectedResidentFullname = resident.fullname; // Store the selected fullname
                                        openModal(modals.schedule); // Open the schedule modal
                                        closeModal('residentModal');
                                        pageContent?.classList.add('blur');
                                    });
                                    residentSearchResults.appendChild(div);
                                });
                            }
                        })
                        .catch((err) => {
                            console.error('Error fetching residents:', err);
                            const errorDiv = document.createElement('div');
                            errorDiv.textContent = 'An error occurred while searching. Please try again.';
                            residentSearchResults.innerHTML = '';
                            residentSearchResults.appendChild(errorDiv);
                        });
                } else {
                    residentSearchResults.innerHTML = ''; // Clear results if query is too short
                }
            });

            // Schedule Form Submission
            scheduleForm?.addEventListener('submit', (e) => {
                e.preventDefault();

                if (!selectedResidentFullname || !selectedResidentId) {
                    alert('Please select a resident from the search first!');
                    return;
                }

                const formData = new FormData(scheduleForm);
                formData.append('resident_id', selectedResidentId); // Resident ID from selected search
                formData.append('fullname', selectedResidentFullname); // Fullname of selected resident
                formData.append('items', JSON.stringify(getSelectedItems())); // Get selected items and pass as JSON string

                fetch('/Brgy Zone 1/add-schedule.php', {
                        method: 'POST',
                        body: formData,
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert('Schedule successfully created!');
                            location.reload(); // Refresh the page or update dynamically
                        } else {
                            alert(data.error || 'An error occurred.');
                        }
                    })
                    .catch((err) => {
                        console.error('Error submitting schedule:', err);
                        alert('There was an error while submitting the schedule. Please try again.');
                    });
            });

            // Helper function to gather selected items
            const getSelectedItems = () => {
                const items = {};
                suppliesList.querySelectorAll('input[type="number"]').forEach((input) => {
                    if (input.value > 0) {
                        items[input.name.replace('supply_', '')] = input.value; // Use item ID as key
                    }
                });
                return items;
            };

            // Initialization
            bindModalCloseButtons();
            initializeModals();
            addResidentBtn?.addEventListener('click', () => openModal(modals.resident));

            // Fetch inventory items and load them into the modal
            // Fetch inventory items and load them into the modal
            const loadSupplies = () => {
                fetch('/Brgy Zone 1/fetch_inventory.php') // Make sure the PHP file is accessible
                    .then(response => response.json())
                    .then(data => {
                        suppliesList.innerHTML = ''; // Clear existing items
                        if (data.length > 0) {
                            data.forEach(supply => {
                                const supplyDiv = document.createElement('div');
                                supplyDiv.classList.add('supply-item');

                                const label = document.createElement('label');
                                label.textContent = `${supply.name} (Available: ${supply.quantity} ${supply.unit})`;
                                supplyDiv.appendChild(label);

                                const input = document.createElement('input');
                                input.type = 'number';
                                input.name = `supply_${supply.id}`;
                                input.min = 0;
                                input.max = supply.quantity;
                                supplyDiv.appendChild(input);

                                suppliesList.appendChild(supplyDiv);
                            });
                        } else {
                            const noSuppliesDiv = document.createElement('div');
                            noSuppliesDiv.textContent = 'No supplies available.';
                            suppliesList.appendChild(noSuppliesDiv);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching inventory:', error);
                        const errorDiv = document.createElement('div');
                        errorDiv.textContent = 'An error occurred while loading the supplies.';
                        suppliesList.appendChild(errorDiv);
                    });
            };
            loadSupplies(); // Load inventory supplies when the modal is opened

            const editModal = document.getElementById("editModal");
            const editForm = document.getElementById("editForm");

            // Event listener for edit buttons
            document.querySelectorAll(".btn-edit").forEach((button) => {
                button.addEventListener("click", () => {
                    // Retrieve data from button attributes
                    const residentId = button.getAttribute("data-resident-id");
                    const fullname = button.getAttribute("data-fullname");
                    const distributionDate = button.getAttribute("data-distribution-date");
                    const status = button.getAttribute("data-status");
                    pageContent?.classList.add('blur');

                    // Populate modal fields
                    document.getElementById("editResidentId").value = residentId;
                    document.getElementById("editFullname").value = fullname;
                    document.getElementById("editDistributionDate").value = distributionDate;
                    document.getElementById("editStatus").value = status;

                    // Fetch and populate supplies for the resident
                    fetch(`/Brgy Zone 1/fetch_resident_supplies.php?resident_id=${residentId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            const editSuppliesList = document.getElementById("editSuppliesList");
                            editSuppliesList.innerHTML = ""; // Clear existing items

                            data.supplies.forEach((supply) => {
                                const supplyDiv = document.createElement("div");
                                supplyDiv.classList.add("supply-item");

                                const label = document.createElement("label");
                                label.textContent = `${supply.name} (Available: ${supply.available} ${supply.unit})`;
                                supplyDiv.appendChild(label);

                                const input = document.createElement("input");
                                input.type = "number";
                                input.name = `supply_${supply.id}`;
                                input.min = 0;
                                input.max = supply.available;
                                input.value = supply.quantity || 0; // Prefill if available
                                supplyDiv.appendChild(input);

                                editSuppliesList.appendChild(supplyDiv);
                            });
                        });

                    // Show the edit modal
                    editModal.style.display = "block";
                });
            });

            // Close modal
            document.querySelector(".btn-cancel").addEventListener("click", () => {
                editModal.style.display = "none";
            });

            // Handle form submission
            editForm.addEventListener("submit", (e) => {
                e.preventDefault();

                const formData = new FormData(editForm);
                formData.append(
                    "items",
                    JSON.stringify(
                        [...document.querySelectorAll("#editSuppliesList input")].reduce((items, input) => {
                            if (input.value > 0) {
                                items[input.name.replace("supply_", "")] = parseInt(input.value, 10);
                            }
                            return items;
                        }, {})
                    )
                );

                fetch("/Brgy Zone 1/edit-schedule.php", {
                        method: "POST",
                        body: formData,
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert("Schedule updated successfully!");
                            location.reload();
                        } else {
                            alert(data.error || "An error occurred.");
                        }
                    })
                    .catch((err) => console.error("Error:", err));
            });

        });

        document.querySelectorAll(".btn-delete").forEach((button) => {
            button.addEventListener("click", () => {
                // Get the resident ID from the button
                const residentId = button.getAttribute("data-resident-id");

                // Populate the hidden input in the delete modal
                document.getElementById("deleteResidentId").value = residentId;

                // Show the delete modal
                const deleteModal = document.getElementById("deleteModal");
                deleteModal.style.display = "block";
            });
        });

        // Close modal function
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Handle delete form submission
        document.getElementById("deleteForm").addEventListener("submit", (event) => {
            event.preventDefault(); // Prevent form submission

            const residentId = document.getElementById("deleteResidentId").value;

            // Send delete request to the server
            fetch("/Brgy Zone 1/delete-schedule.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        resident_id: residentId
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Schedule deleted successfully.");
                        closeModal("deleteModal");
                        // Optionally refresh the table to reflect the deletion
                        location.reload();
                    } else {
                        alert(`Failed to delete schedule: ${data.error}`);
                    }
                })
                .catch((error) => {
                    alert(`Error: ${error.message}`);
                });
        });


        document.addEventListener("DOMContentLoaded", () => {
            const logoutLink = document.getElementById("logout-link"); // Link that triggers the logout modal
            const logoutModal = document.getElementById("logout-modal"); // Modal element
            const confirmLogout = document.getElementById("confirm-logout"); // Confirm button
            const cancelLogout = document.getElementById("cancel-logout"); // Cancel button
            const closeSpan = logoutModal.querySelector(".close"); // Close button (X)

            // Function to open the modal
            const openModal = () => {
                logoutModal.style.display = "block";
                document.body.style.overflow = "hidden"; // Disable background scroll
            };

            // Function to close the modal
            const closeModal = () => {
                logoutModal.style.display = "none";
                document.body.style.overflow = "auto"; // Enable background scroll
            };

            // Event listener to open the modal
            logoutLink?.addEventListener("click", (e) => {
                e.preventDefault(); // Prevent navigation
                openModal();
            });

            // Event listener for confirm logout
            confirmLogout?.addEventListener("click", () => {
                window.location.href = logoutLink.href; // Redirect to logout
            });

            // Event listeners to close the modal
            cancelLogout?.addEventListener("click", closeModal);
            closeSpan?.addEventListener("click", closeModal);
            window.addEventListener("click", (e) => {
                if (e.target === logoutModal) closeModal(); // Close if clicking outside modal
            });
        });


        document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("residentTable");
    const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    const searchBar = document.getElementById("searchBar");
    const paginationControls = document.getElementById("paginationControls");
    const rowsPerPage = 5;
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
        paginationControls.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement("button");
            pageButton.textContent = i;
            pageButton.className = i === currentPage ? "active" : "";
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
                rows[i].style.display = "";
                visibleRowCount++;
            } else {
                rows[i].style.display = "none";
            }
        }

        // Update pagination after filtering
        if (filter === "") {
            displayPage(1);
        } else {
            currentPage = 1;
            updatePaginationControls();
        }
    });

    // Initial Setup
    displayPage(currentPage);
});

    </script>

</body>

</html>