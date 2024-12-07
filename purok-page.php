<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purok Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            display: flex; /* Use flexbox to align items */
            align-items: center; /* Vertically center items */
            flex-wrap: wrap; /* Ensure proper wrapping on smaller devices */
            position: relative; /* To position the back button */
        }

        /* Logo Styles */
        .logo-container {
            text-align: center;
            margin-right: 20px; /* Space between logo and title */
        }

        .logo-container img {
            height: 60px;
            width: 60px;
            object-fit: cover;
            border-radius: 50%; /* Make the logo circular */
            border: 2px solid white; /* Optional: Add a white border for better visibility */
        }

        .logo-container span {
            display: block;
            margin-top: 5px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: 1.8rem;
            flex: 1; /* Push the header text to take remaining space */
            text-align: center; /* Center align the text on smaller devices */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Back Button */
        .back-button {
            position: absolute;
            top: 10px; /* Adjusted positioning for top alignment */
            right: 20px; /* Align to the right corner */
            background-color: white;
            color: #007bff;
            border: 2px solid #007bff;
            border-radius: 5px;
            padding: 5px 10px; /* Smaller padding for a compact button */
            font-size: 14px; /* Reduced font size */
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px; /* Space between the icon and text */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .back-button:hover {
            background-color: #007bff;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px; /* Optional: Small padding for consistency */
        }

        /* Grid Layout for Purok */
        .purok-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Responsive grid */
            gap: 20px; /* Add space between cards */
            justify-content: center; /* Center items horizontally */
        }

        /* Purok Card */
        .purok-card {
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.15), -5px -5px 10px rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column; /* Align contents vertically */
            justify-content: space-between; /* Space between items */
            height: 200px; /* Ensure consistent height */
        }

        .purok-card:hover {
            transform: translateY(-10px); /* Lift the card for a 3D effect */
            box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.2), -10px -10px 20px rgba(255, 255, 255, 0.9);
        }

        .purok-card i {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 10px;
            animation: bounce 2s infinite;
        }

        /* Add subtle bounce animation to folder icon */
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .purok-card h3 {
            font-size: 1.5rem;
            color: #333;
            margin: 10px 0;
            font-weight: bold;
            letter-spacing: 1px;
            flex-grow: 1; /* Allow the heading to take available space */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .purok-card a {
            display: inline-block;
            margin-top: 10px;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.15);
            align-self: center; /* Center the button horizontally */
        }

        .purok-card a:hover {
            background-color: #0056b3;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="logo.jpg" alt="Barangay Logo">
            <span>AIDMAN</span>
        </div>
        <h1>Barangay Purok Information</h1>
        <a href="admin-dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </header>

    <div class="container">
        <div class="purok-grid">
            <?php
            // Fetch ENUM values for Purok dynamically
            $sql = "SHOW COLUMNS FROM resident_list WHERE Field = 'purok'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Extract ENUM values
                $enumList = $row['Type']; // e.g., "enum('Value1','Value2',...)"
                preg_match_all("/'([^']+)'/", $enumList, $matches); // Extract values using regex
                $purokEnumValues = $matches[1]; // Get the list of ENUM values

                foreach ($purokEnumValues as $purok) {
                    echo "
                    <div class='purok-card'>
                        <i class='fas fa-folder'></i>
                        <h3>$purok</h3>
                        <a href='view-purok.php?purok=" . urlencode($purok) . "'>View Details</a>
                    </div>
                    ";
                }
            } else {
                echo "<p>No purok information found in the database.</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Barangay Management System. All Rights Reserved.</p>
    </footer>
</body>
</html>
