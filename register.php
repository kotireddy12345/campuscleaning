<?php
require 'config.php';
$data = getJsonInput();
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$role = trim($data['role'] ?? 'user');
$campus = trim($data['campus'] ?? ''); // Optional now

if (empty($name) || empty($email) || empty($password) || empty($role)) {
    jsonResponse(false, "All fields are required", null, 400);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO users (name, email, password, role, campus) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $hash, $role, $campus);

if ($stmt->execute()) {
    jsonResponse(true, "User registered successfully");
} else {
    jsonResponse(false, "Registration failed", null, 500);
}
?>