<?php
require 'db_connect.php';

// Get the input from the request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['mobile_number'], $data['message'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$mobileNumber = $data['mobile_number'];
$message = $data['message'];

// Example using Twilio to send SMS
// Include the Twilio PHP SDK (install via Composer if needed)
require_once '/path/to/vendor/autoload.php'; 

use Twilio\Rest\Client;

// Twilio account SID and Auth Token (Replace with your own)
$sid = 'your_account_sid';
$auth_token = 'your_auth_token';
$twilio_number = 'your_twilio_phone_number';

$client = new Client($sid, $auth_token);

try {
    // Send the SMS
    $client->messages->create(
        $mobileNumber, // To the resident's mobile number
        [
            'from' => $twilio_number, // From your Twilio number
            'body' => $message
        ]
    );

    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send message: ' . $e->getMessage()]);
}
?>
