<?php
include 'db_connect.php';

header('Content-Type: application/json');

$response = array();

// Get total users
$sql = "SELECT COUNT(*) AS total_all FROM users";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_all'] = (int) $row['total_all'];
} else {
    $response['total_all'] = 0;
}

// Get total officials
$sql = "SELECT COUNT(*) AS total_officials FROM users WHERE role = 'Official'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_officials'] = (int) $row['total_officials'];
} else {
    $response['total_officials'] = 0;
}

// Get total admins
$sql = "SELECT COUNT(*) AS total_admin FROM users WHERE role = 'Admin'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_admin'] = (int) $row['total_admin'];
} else {
    $response['total_admin'] = 0;
}

// Fetch ENUM values for purok dynamically
$sql = "SHOW COLUMNS FROM resident_list WHERE Field = 'purok'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Extract ENUM values from the database metadata
    $enumList = $row['Type']; // e.g., "enum('Value1','Value2',...)"
    preg_match_all("/'([^']+)'/", $enumList, $matches); // Extract values using regex
    $purokEnumValues = $matches[1]; // Get the list of ENUM values
    $response['total_purok'] = count($purokEnumValues); // Count total ENUM values
} else {
    $response['total_purok'] = 0; // Default to 0 if no ENUM values are found
}

// Get total residents in the resident_list table
$sql = "SELECT COUNT(*) AS total_residents FROM resident_list";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_residents'] = (int) $row['total_residents'];
} else {
    $response['total_residents'] = 0;
}

// Get total calamities in the calamity_list table
$sql = "SELECT COUNT(*) AS total_calamities FROM calamity_list";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_calamities'] = (int) $row['total_calamities'];
} else {
    $response['total_calamities'] = 0;
}

// Output the response as JSON
echo json_encode($response, JSON_PRETTY_PRINT);

$conn->close();
?>
