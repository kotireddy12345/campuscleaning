<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'config.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->email) || empty($data->otp)) {
    jsonResponse(false, "Email and OTP are required");
}

$email = trim($data->email);
$otp = trim($data->otp);

// Check if OTP exists and is valid
$stmt = $mysqli->prepare("SELECT * FROM password_resets WHERE email = ? AND otp = ? AND expires_at > NOW()");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    jsonResponse(false, "Invalid or expired OTP");
}

// OTP is valid - generate a reset token
$reset_token = bin2hex(random_bytes(32));

// Update the record with reset token
$updateStmt = $mysqli->prepare("UPDATE password_resets SET reset_token = ? WHERE email = ?");
$updateStmt->bind_param("ss", $reset_token, $email);
$updateStmt->execute();

jsonResponse(true, "OTP verified successfully", ["reset_token" => $reset_token]);

$stmt->close();
$mysqli->close();
?>