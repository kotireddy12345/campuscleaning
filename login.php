<?php
require 'config.php';

$data = getJsonInput();
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$stmt = $mysqli->prepare("SELECT id as user_id, name, email, password, role, campus FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    jsonResponse(false, "Invalid email or password", null, 401);
}

$user = $result->fetch_assoc();

if (password_verify($password, $user['password'])) {
    unset($user['password']); // Never send the password hash back
    jsonResponse(true, "Login successful", $user);
} else {
    jsonResponse(false, "Invalid email or password", null, 401);
}
?>