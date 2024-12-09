<?php
// Ensure the user is authenticated as admin
include 'check_admin.php';

// Include database connection
require 'db_connect.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $calamity = htmlspecialchars(trim($_POST['calamity']));
    $residentName = htmlspecialchars(trim($_POST['resident_name'])); // Full name input from the form
    $purok = htmlspecialchars(trim($_POST['purok']));
    $mobileNumber = htmlspecialchars(trim($_POST['mobile_number']));
    $damageSeverity = floatval($_POST['damage_severity']);
    $occupants = intval($_POST['occupants']);
    $vulnerability = floatval($_POST['vulnerability']);
    $incomeLevel = floatval($_POST['income_level']);
    $specialNeeds = floatval($_POST['special_needs']);

    // Validate required inputs
    if (empty($calamity) || empty($residentName) || empty($purok) || empty($mobileNumber)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Calculate the ranking score
    $rankingScore = ($damageSeverity * 0.3) +
                    ($occupants * 0.2) +
                    ($vulnerability * 0.2) +
                    ($incomeLevel * 0.15) +
                    ($specialNeeds * 0.15);

    try {
        // Get the resident's ID from the resident_list table
        $residentQuery = "SELECT id FROM resident_list WHERE CONCAT(firstname, ' ', middlename, ' ', lastname, ' ', suffix) = '$residentName'";
        $residentResult = $conn->query($residentQuery);

        if ($residentResult && $residentResult->num_rows > 0) {
            $row = $residentResult->fetch_assoc();
            $id = $row['id'];

            // Check if the combination of id and calamity already exists
            $checkQuery = "SELECT COUNT(*) as count FROM resident_ranking WHERE id = $id AND calamity = '$calamity'";
            $checkResult = $conn->query($checkQuery);
            $checkRow = $checkResult->fetch_assoc();

            if ($checkRow['count'] > 0) {
                // Combination exists, do not insert
                echo "<script>alert('This resident is already listed for the selected calamity.'); window.history.back();</script>";
            } else {
                // Insert the ranking data into the resident_ranking table
                $insertQuery = "
                    INSERT INTO resident_ranking 
                    (id, calamity, resident_name, purok, mobile_number, damage_severity, occupants, vulnerability, income_level, special_needs, ranking_score) 
                    VALUES 
                    ($id, '$calamity', '$residentName', '$purok', '$mobileNumber', $damageSeverity, $occupants, $vulnerability, $incomeLevel, $specialNeeds, $rankingScore)
                ";

                if ($conn->query($insertQuery) === TRUE) {
                    echo "<script>alert('Ranking registered successfully!'); window.location.href = 'ranking_resident.php';</script>";
                } else {
                    throw new Exception("Error executing insert query: " . $conn->error);
                }
            }
        } else {
            // Resident not found in the resident_list
            throw new Exception("The resident does not exist in the resident list.");
        }
    } catch (Exception $e) {
        // Safely output the error message
        $errorMessage = json_encode($e->getMessage());
        echo "<script>alert('An error occurred: $errorMessage'); window.history.back();</script>";
    } finally {
        // Close the database connection
        $conn->close();
    }
} else {
    // Redirect if accessed without POST
    header("Location: ranking_resident.php");
    exit();
}
?>
