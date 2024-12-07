<?php
include 'db_connect.php';

$rank_message = ""; // Initialize rank message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $damage_severity = $_POST['damage_severity'];
    $number_of_occupants = $_POST['number_of_occupants'];
    $vulnerability = $_POST['vulnerability'];
    $income_level = $_POST['income_level'];
    $special_needs = $_POST['special_needs'];

    // Define weights
    $weights = [
        'damage_severity' => 0.30,
        'number_of_occupants' => 0.20,
        'vulnerability' => 0.20,
        'income_level' => 0.15,
        'special_needs' => 0.15
    ];

    // Calculate total score
    $total_score = ($damage_severity * $weights['damage_severity']) +
                   ($number_of_occupants * $weights['number_of_occupants']) +
                   ($vulnerability * $weights['vulnerability']) +
                   ($income_level * $weights['income_level']) +
                   ($special_needs * $weights['special_needs']);

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO residents (name, damage_severity, number_of_occupants, vulnerability, income_level, special_needs, total_score) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiiid", $name, $damage_severity, $number_of_occupants, $vulnerability, $income_level, $special_needs, $total_score);

    if ($stmt->execute()) {
        // Fetch total count of residents for ranking
        $result = $conn->query("SELECT COUNT(*) as count FROM residents WHERE total_score >= $total_score");
        $row = $result->fetch_assoc();
        $rank = $row['count'];

        $rank_message = "Your rank is number $rank based on your total score.";
        $success_message = "Registration successful.";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Aid</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom right, #2c3e50, #bdc3c7);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .submit_aid-container {
            display: flex;
            justify-content: center;
            align-items: center;
            animation: drop 1s ease forwards;
        }

        @keyframes drop {
            0% {
                transform: translateY(-80px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .submit_aid-card {
            background: linear-gradient(135deg, #ffffff, #f0f0f0);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            width: 450px;
            text-align: center;
            transition: transform 0.3s ease, opacity 0.5s ease;
            opacity: 0;
            animation: fadeIn 1s forwards ease-in-out;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        h2 {
            margin: 0;
            padding: 0 0 10px 0;
            font-size: 28px;
            color: #2c3e50;
            font-weight: bold;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        button {
            padding: 14px 22px;
            border: none;
            border-radius: 25px;
            background: linear-gradient(90deg, #2980b9, #6dd5ed);
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            margin-top: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            background: linear-gradient(90deg, #1a242f, #34495e);
            transform: scale(1.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .error {
            color: #e74c3c;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            border: 2px solid #2980b9;
            border-radius: 10px;
            transition: border-color 0.3s, transform 0.2s, box-shadow 0.3s;
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #1abc9c;
            outline: none;
            transform: scale(1.03);
            box-shadow: 0 4px 15px rgba(0, 128, 128, 0.3);
        }

        label {
            margin-top: 10px;
            display: block;
            color: #34495e;
            font-weight: bold;
            text-transform: uppercase;
        }

        .submit_aid-card::before {
            content: "";
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 5px;
            background: #2980b9;
            border-radius: 10px;
        }

        @media (max-width: 480px) {
            .submit_aid-card {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="submit_aid-container">
    <div class="submit_aid-card <?php echo isset($success_message) ? 'show' : ''; ?>">
        <?php
        if (isset($success_message)) {
            echo "<h2>Thank You!</h2>";
            echo "<p>{$success_message}</p>";
            echo "<p>{$rank_message}</p>";
            echo '<button id="redirect-btn">Go to Dashboard</button>';
        } elseif (isset($error_message)) {
            echo "<h2>Error!</h2>";
            echo "<p class='error'>{$error_message}</p>";
        } else {
            // Show the form
            ?>
            <form method="POST" action="submit_aid.php">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="damage_severity">Damage Severity (1-10):</label>
                <input type="number" id="damage_severity" name="damage_severity" min="1" max="10" required>

                <label for="number_of_occupants">Number of Occupants:</label>
                <input type="number" id="number_of_occupants" name="number_of_occupants" min="1" required>

                <label for="vulnerability">Vulnerability (1-10):</label>
                <input type="number" id="vulnerability" name="vulnerability" min="1" max="10" required>

                <label for="income_level">Income Level (1-10):</label>
                <input type="number" id="income_level" name="income_level" min="1" max="10" required>

                <label for="special_needs">Special Needs (1-10):</label>
                <input type="number" id="special_needs" name="special_needs" min="1" max="10" required>

                <button type="submit">Submit</button>
            </form>
            <?php
        }
        ?>
    </div>
</div>

<script>
    // JavaScript for redirect button functionality
    const redirectBtn = document.getElementById('redirect-btn');
    if (redirectBtn) {
        redirectBtn.addEventListener('click', function() {
            window.location.href = 'aid-dashboard.php';
        });
    }

    // Show the card on load if there's a success message
    window.addEventListener('DOMContentLoaded', function() {
        if (<?php echo isset($success_message) ? 'true' : 'false'; ?>) {
            document.querySelector('.submit_aid-card').classList.add('show');
        }
    });
</script>

</body>
</html>