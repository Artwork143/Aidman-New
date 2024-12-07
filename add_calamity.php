<?php
// add_calamity.php

// Include database connection
require 'db_connect.php';

// Initialize variables
$name = $type = $date_occurred = $severity_level = $description = "";
$nameErr = $typeErr = $dateErr = $severityLevelErr = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = $_POST["name"];
    }

    // Validate type
    if (empty($_POST["type"])) {
        $typeErr = "Type is required";
    } else {
        $type = $_POST["type"];
    }

    // Validate date occurred
    if (empty($_POST["date_occurred"])) {
        $dateErr = "Date occurred is required";
    } else {
        $date_occurred = $_POST["date_occurred"];
    }

    // Validate severity level
    if (empty($_POST["severity_level"])) {
        $severityLevelErr = "Severity level is required";
    } else {
        $severity_level = $_POST["severity_level"];
    }

    // Get description (optional)
    $description = $_POST["description"];

    // Insert data into the database if no errors
    if (empty($nameErr) && empty($typeErr) && empty($dateErr) && empty($severityLevelErr)) {
        $sql = "INSERT INTO calamity_list (name, type, date_occurred, severity_level, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $type, $date_occurred, $severity_level, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Calamity added successfully!'); window.location.href='calamity_list.php';</script>";
        } else {
            echo "<script>alert('Error: Unable to add calamity. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Calamity</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container {
            width: 50%;
            margin: auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .form-group .error {
            color: red;
            font-size: 0.9rem;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-btn:hover {
            background: #0056b3;
        }
    </style>
    <script>
        // Function to update the severity level options based on the selected type
        function updateSeverityOptions() {
            var type = document.getElementById("type").value;
            var severityLevel = document.getElementById("severity_level");
            var inputField = document.getElementById("magnitude_input");
            severityLevel.innerHTML = ""; // Clear current options
            inputField.style.display = "none"; // Hide magnitude input

            if (type == "Typhoon") {
                var options = ["Signal 1", "Signal 2", "Signal 3", "Signal 4", "Signal 5"];
                options.forEach(function(option) {
                    var opt = document.createElement("option");
                    opt.value = option;
                    opt.text = option;
                    severityLevel.appendChild(opt);
                });
            } else if (type == "Flood") {
                var options = ["Minor Flooding", "Moderate Flooding", "Severe Flooding"];
                options.forEach(function(option) {
                    var opt = document.createElement("option");
                    opt.value = option;
                    opt.text = option;
                    severityLevel.appendChild(opt);
                });
            } else if (type == "Fire Incident") {
                var options = ["Alert 1", "Alert 2", "Alert 3"];
                options.forEach(function(option) {
                    var opt = document.createElement("option");
                    opt.value = option;
                    opt.text = option;
                    severityLevel.appendChild(opt);
                });
            } else if (type == "Earthquake") {
                var options = [];
                for (var i = 1; i <= 10; i++) {
                    options.push(i);
                }
                options.forEach(function(option) {
                    var opt = document.createElement("option");
                    opt.value = option;
                    opt.text = option;
                    severityLevel.appendChild(opt);
                });
                // Show magnitude input field for earthquakes
                inputField.style.display = "inline-block";
            }
        }

        window.onload = updateSeverityOptions; // Load initial severity options based on any preset type
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Add Calamity</h2>
        <form action="add_calamity.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <span class="error"><?php echo $nameErr; ?></span>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <select id="type" name="type" onchange="updateSeverityOptions()">
                    <option value="">Select type</option>
                    <option value="Typhoon" <?php if ($type == "Typhoon") echo "selected"; ?>>Typhoon</option>
                    <option value="Flood" <?php if ($type == "Flood") echo "selected"; ?>>Flood</option>
                    <option value="Earthquake" <?php if ($type == "Earthquake") echo "selected"; ?>>Earthquake</option>
                    <option value="Fire Incident" <?php if ($type == "Fire Incident") echo "selected"; ?>>Fire Incident</option>
                </select>
                <span class="error"><?php echo $typeErr; ?></span>
            </div>
            <div class="form-group">
                <label for="date_occurred">Date Occurred:</label>
                <input type="date" id="date_occurred" name="date_occurred" value="<?php echo htmlspecialchars($date_occurred); ?>">
                <span class="error"><?php echo $dateErr; ?></span>
            </div>
            <div class="form-group">
                <label for="severity_level">Severity Level:</label>
                <select id="severity_level" name="severity_level">
                    <option value="">Select severity level</option>
                </select>
                <span class="error"><?php echo $severityLevelErr; ?></span>
            </div>
            <div id="magnitude_input" class="form-group" style="display: none;">
                <label for="magnitude">Magnitude (Optional):</label>
                <input type="text" id="magnitude" name="magnitude" placeholder="e.g. 7.2">
            </div>
            <div class="form-group">
                <label for="description">Description (optional):</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <button type="submit" class="submit-btn">Add Calamity</button>
        </form>
    </div>
</body>
</html>
