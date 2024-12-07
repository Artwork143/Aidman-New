<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=aidman-db', 'root', '');

// Fetch data if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM residents WHERE id = ?');
    $stmt->execute([$id]);
    $resident = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resident) {
        die('Resident not found');
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve form data
        $name = $_POST['name'];
        $damage_severity = $_POST['damage_severity'];
        $number_of_occupants = $_POST['number_of_occupants'];
        $vulnerability = $_POST['vulnerability'];
        $income_level = $_POST['income_level'];
        $special_needs = $_POST['special_needs'];

        // Calculate total score based on criteria
        $total_score = $damage_severity * 0.4 +
                       $number_of_occupants * 0.2 +
                       $vulnerability * 0.2 +
                       $income_level * 0.15 +
                       $special_needs * 0.15;

        // Update database
        try {
            $stmt = $pdo->prepare('UPDATE residents SET name = ?, damage_severity = ?, number_of_occupants = ?, vulnerability = ?, income_level = ?, special_needs = ?, total_score = ? WHERE id = ?');
            $stmt->execute([$name, $damage_severity, $number_of_occupants, $vulnerability, $income_level, $special_needs, $total_score, $id]);

            // Redirect to the same page with success parameter
            header('Location: official-edit-data.php?id=' . urlencode($id) . '&success=1');
            exit;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
} else {
    die('ID not provided');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Resident</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="leader-edit-body">
    <!-- Back button with Font Awesome icon -->
    <div class="back-button-container">
        <a class="back-button" href="official-aid-dashboard.php">
            <i class="fas fa-arrow-left back-icon"></i>
            Back
        </a>
    </div>

    <div class="leader-edit-container">
        <h3 class="leader-edit-h3">Resident Registration Form</h3>
        <form class="leader-edit-form" action="official-edit-data.php?id=<?php echo urlencode($id); ?>" method="post">
            <label class="leader-edit-label" for="name">Name:</label>
            <input class="leader-edit-input" type="text" id="name" name="name" value="<?php echo htmlspecialchars($resident['name']); ?>" required><br>
            
            <label class="leader-edit-label" for="damage_severity">Damage Severity (1-10):</label>
            <input class="leader-edit-input" type="number" id="damage_severity" name="damage_severity" min="1" max="10" value="<?php echo htmlspecialchars($resident['damage_severity']); ?>" required><br>
            
            <label class="leader-edit-label" for="number_of_occupants">Number of Occupants:</label>
            <input class="leader-edit-input" type="number" id="number_of_occupants" name="number_of_occupants" value="<?php echo htmlspecialchars($resident['number_of_occupants']); ?>" required><br>
            
            <label class="leader-edit-label" for="vulnerability">Vulnerability (1-10):</label>
            <input class="leader-edit-input" type="number" id="vulnerability" name="vulnerability" min="1" max="10" value="<?php echo htmlspecialchars($resident['vulnerability']); ?>" required><br>
            
            <label class="leader-edit-label" for="income_level">Income Level (1-10):</label>
            <input class="leader-edit-input" type="number" id="income_level" name="income_level" min="1" max="10" value="<?php echo htmlspecialchars($resident['income_level']); ?>" required><br>
            
            <label class="leader-edit-label" for="special_needs">Special Needs (1-10):</label>
            <input class="leader-edit-input" type="number" id="special_needs" name="special_needs" min="1" max="10" value="<?php echo htmlspecialchars($resident['special_needs']); ?>" required><br>
            
            <input class="leader-edit-input" type="submit" value="Submit">
        </form>
    </div>

    <script>
        // Show SweetAlert if the success parameter is set
        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Edited Successfully',
            text: 'The resident information has been updated.',
            confirmButtonText: 'OK'
        });
        <?php endif; ?>
    </script>
</body>
</html>
