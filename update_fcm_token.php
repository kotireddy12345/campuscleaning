<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'config.php';

// Log incoming request for debugging
error_log("=== FCM Token Update Request ===");
error_log("Raw input: " . file_get_contents("php://input"));

$data = getJsonInput();
error_log("Parsed data: " . json_encode($data));

$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$fcm_token = isset($data['fcm_token']) ? trim($data['fcm_token']) : '';

error_log("User ID: $user_id");
error_log("FCM Token: " . substr($fcm_token, 0, 50) . "...");

if ($user_id <= 0) {
    error_log("ERROR: Invalid user ID");
    jsonResponse(false, "Valid user ID is required");
}

if (empty($fcm_token)) {
    error_log("ERROR: Empty FCM token");
    jsonResponse(false, "FCM token is required");
}

// Update user's FCM token
$stmt = $mysqli->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
$stmt->bind_param("si", $fcm_token, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        jsonResponse(true, "FCM token updated successfully");
    } else {
        // Check if user exists
        $checkStmt = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
        $checkStmt->bind_param("i", $user_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows == 0) {
            jsonResponse(false, "User not found");
        } else {
            jsonResponse(true, "FCM token is already up to date");
        }
        $checkStmt->close();
    }
} else {
    jsonResponse(false, "Database error: " . $stmt->error);
}

$stmt->close();
$mysqli->close();
?>