<?php
require 'config.php';

$data = getJsonInput();

$name      = trim($data['name']      ?? '');
$email     = trim($data['email']     ?? '');
$password  = trim($data['password']  ?? '');
$campus    = trim($data['campus']    ?? '');
$role      = $data['role'] ?? 'manager'; // manager / staff

// ---------- BASIC REQUIRED CHECK ----------
if ($name === '' || $email === '' || $password === '' || $campus === '') {
    jsonResponse(false, 'Name, email, campus and password are required', null, 400);
}

// ---------- EMAIL VALIDATION ----------
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, 'Invalid email address format', null, 400);
}

// ---------- CHECK EMAIL ALREADY EXISTS ----------
$stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    jsonResponse(false, 'Email already registered', null, 409);
}
$stmt->close();

// ---------- HASH PASSWORD ----------
$hash = password_hash($password, PASSWORD_BCRYPT);

// ---------- INSERT USER ----------
$stmt = $mysqli->prepare(
    "INSERT INTO users (name, email, password_hash, campus, role)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param('sssss', $name, $email, $hash, $campus, $role);

if ($stmt->execute()) {
    jsonResponse(true, 'User registered successfully', [
        'user_id' => $stmt->insert_id,
        'name'    => $name,
        'email'   => $email,
        'campus'  => $campus,
        'role'    => $role
    ], 201);
} else {
    jsonResponse(false, 'Failed to register user', null, 500);
}
