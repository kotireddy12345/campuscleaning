<?php
// Set headers for JSON response and enable error reporting for debugging
header("Content-Type: application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- DATABASE CONFIGURATION ---
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "campusclean";

// Create MySQLi connection
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    // Use a helper function for consistent JSON responses
    jsonResponse(false, "Database connection failed: " . $mysqli->connect_error, null, 500);
}

// Helper function to get JSON input from the app
function getJsonInput() {
    return json_decode(file_get_contents("php://input"), true);
}

// Helper function to send a standard JSON response and exit
function jsonResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = ["success" => $success, "message" => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}
?>