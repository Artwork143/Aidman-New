<?php
include 'db_connect.php';

// Ensure $purok is defined and sanitized
$purok = isset($_GET['purok']) ? urldecode($_GET['purok']) : '';

if (empty($purok)) {
    die("Invalid or missing Purok parameter.");
}

// Fetch residents in the selected Purok
$sql = "SELECT fullname, gender, age, marital_status FROM resident_list WHERE purok = ? ORDER BY fullname ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $purok);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents in <?php echo htmlspecialchars($purok); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .print-container {
            width: 210mm; /* A4 size width */
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .print-header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .print-header img {
            width: 80px;
            height: 80px;
            margin-right: 20px;
        }

        .print-header .header-text {
            text-align: center;
            flex-grow: 1;
        }

        .print-header .header-text h1 {
            margin: 0;
            font-size: 1.8rem;
            color: #007bff;
            text-transform: uppercase;
        }

        .print-header .header-text p {
            margin: 5px 0 0;
            font-size: 1rem;
            color: #333;
        }

        .back-button {
            color: white;
            background-color: #28a745;
            padding: 10px 15px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 14px;
            position: fixed;
            top: 20px;
            left: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
        }

        .back-button:hover {
            background-color: #218838;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.4);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .print-button {
            color: white;
            background-color: #007bff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 12px;
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #007bff;
            color: white;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        /* Remove default header and footer (date, time, URL, etc.) */
        @page {
            margin: 0;
        }

        /* Print Styles */
        @media print {
            .print-button,
            .back-button,
            footer {
                display: none;
            }

            .print-container {
                box-shadow: none;
                border: none;
                width: 100%;
                padding: 0;
            }

            body {
                background-color: white;
            }
        }
    </style>
</head>
<body>
    <a href="purok-page2.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="print-container">
        <a href="#" class="print-button" onclick="window.print();">
            <i class="fas fa-print"></i> Print
        </a>

        <div class="print-header">
            <img src="logo.jpg" alt="Barangay Logo">
            <div class="header-text">
                <h1>Barangay Zone 1</h1>
                <p>Residents of <?php echo htmlspecialchars($purok); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Marital Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $counter = 1; // Start numbering from 1 ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['marital_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No residents found in this Purok.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Barangay Management System. All Rights Reserved.</p>
    </footer>
</body>
</html>
