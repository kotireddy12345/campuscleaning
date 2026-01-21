<?php
require 'config.php';

$user_id = intval($_GET['user_id'] ?? 0);
if ($user_id <= 0) {
    jsonResponse(false, "User ID is required", null, 400);
}

$stmt = $mysqli->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result) {
    while($row = $result->fetch_assoc()) $data[] = $row;
}

jsonResponse(true, "User complaints fetched", $data);
?>