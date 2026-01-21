<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'config.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->email) || empty($data->reset_token) || empty($data->new_password)) {
    jsonResponse(false, "Email, reset token and new password are required");
}

$email = trim($data->email);
$reset_token = trim($data->reset_token);
$new_password = trim($data->new_password);

// Verify reset token
$stmt = $mysqli->prepare("SELECT * FROM password_resets WHERE email = ? AND reset_token = ? AND expires_at > NOW()");
$stmt->bind_param("ss", $email, $reset_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    jsonResponse(false, "Invalid or expired reset token");
}

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update user's password
$updateStmt = $mysqli->prepare("UPDATE users SET password = ? WHERE email = ?");
$updateStmt->bind_param("ss", $hashed_password, $email);

if ($updateStmt->execute()) {
    // Delete the password reset record
    $deleteStmt = $mysqli->prepare("DELETE FROM password_resets WHERE email = ?");
    $deleteStmt->bind_param("s", $email);
    $deleteStmt->execute();

    jsonResponse(true, "Password reset successfully");
} else {
    jsonResponse(false, "Failed to reset password");
}

$stmt->close();
$mysqli->close();
?>