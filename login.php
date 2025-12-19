<?php
require 'config.php';

$data = getJsonInput();

$email    = trim($data['email']    ?? '');
$password = trim($data['password'] ?? '');

if ($email === '' || $password === '') {
    jsonResponse(false, 'Email and password are required', null, 400);
}

// find user
$stmt = $mysqli->prepare("SELECT id, name, email, password_hash, role, campus FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, 'Invalid email or password', null, 401);
}

$user = $result->fetch_assoc();

// verify password
if (!password_verify($password, $user['password_hash'])) {
    jsonResponse(false, 'Invalid email or password', null, 401);
}

// for now we just return user details (no tokens)
$jsonUser = [
    'user_id' => (int)$user['id'],
    'name'    => $user['name'],
    'email'   => $user['email'],
    'role'    => $user['role'],
    'campus'  => $user['campus']
];

jsonResponse(true, 'Login successful', $jsonUser);
