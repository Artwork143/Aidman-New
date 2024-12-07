<?php
// Database connection
require 'db_connect.php';

// Check if the database connection is established
if (!$conn) {
    echo "<p>Failed to connect to the database: " . mysqli_connect_error() . "</p>";
    exit;
}

// Check if the 'id' parameter is set in the query string and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid calamity ID provided: No valid ID found in the request.</p>";
    exit;
}

// Fetch calamity ID from the query string
$calamity_id = intval($_GET['id']);

if ($calamity_id > 0) {
    // Fetch calamity details from the database
    $sql = "SELECT * FROM calamity_list WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<p>Failed to prepare statement: " . $conn->error . "</p>";
        exit;
    }
    $stmt->bind_param("i", $calamity_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $calamity = $result->fetch_assoc();
    } else {
        echo "<p>Calamity not found. No record with ID: " . $calamity_id . "</p>";
        exit;
    }
} else {
    echo "<p>Invalid calamity ID provided: " . htmlspecialchars($_GET['id']) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calamity View - <?php echo htmlspecialchars($calamity['name']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .calamity-title {
            font-size: 32px;
            color: #333;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .calamity-details {
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .calamity-details p {
            font-size: 18px;
            color: #666;
            line-height: 1.8;
        }

        .folder-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .folder-card {
            flex: 0 0 48%;
            background: #f8f9fa;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .folder-card:hover {
            transform: translateY(-5px);
        }

        .folder-card i {
            font-size: 30px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .folder-card a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .folder-card a:hover {
            text-decoration: none;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            padding: 10px 15px;
            background: #28a745;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
            font-size: 14px;
        }

        .back-button:hover {
            background: #218838;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .action-buttons a {
            text-decoration: none;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .action-buttons a:hover {
            background: #0056b3;
        }

        .delete-button {
            background: #dc3545;
        }

        .delete-button:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <a href="calamity_list.php" class="back-button">Back</a>
    <div class="container">
        <div class="calamity-title"><?php echo htmlspecialchars($calamity['name']); ?></div>
        <?php if (isset($calamity)) : ?>
            <div class="calamity-details">
                <p><strong>Type:</strong> <?php echo htmlspecialchars($calamity['type']); ?></p>
                <p><strong>Date Occurred:</strong> <?php echo htmlspecialchars($calamity['date_occurred']); ?></p>
                <p><strong>Severity:</strong> <?php echo htmlspecialchars($calamity['severity_level']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($calamity['description']); ?></p>
            </div>

            <div class="folder-container">
                <div class="folder-card">
                    <i class="fas fa-folder-open"></i>
                    <p><a href="ranking_folder.php?calamity_id=<?php echo $calamity_id; ?>">Ranking Folder</a></p>
                </div>
                <div class="folder-card">
                    <i class="fas fa-users"></i>
                    <p><a href="resident_received_folder.php?calamity_id=<?php echo $calamity_id; ?>">Resident Received Folder</a></p>
                </div>
            </div>

            <div class="action-buttons">
                <a href="edit_calamity.php?calamity_id=<?php echo $calamity_id; ?>" class="edit-button">Edit</a>
                <a href="delete_calamity.php?calamity_id=<?php echo $calamity_id; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this calamity?');">Delete</a>
            </div>
        <?php else : ?>
            <p>Sorry, the details for this calamity could not be found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
