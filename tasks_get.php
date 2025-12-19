<?php
require 'config.php';

$task_id = $_GET['task_id'] ?? 1;

if (!$task_id) {
    jsonResponse(false, 'task_id is required', 1, 400);
}

$stmt = $mysqli->prepare(
    "SELECT t.*, 
            b.name AS building_name,
            r.name AS room_name
     FROM tasks t
     LEFT JOIN buildings b ON t.building_id = b.id
     LEFT JOIN rooms r ON t.room_id = r.id
     WHERE t.id = ?"
);
$stmt->bind_param('i', $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, 'Task not found', null, 404);
}

$task = $result->fetch_assoc();
jsonResponse(true, 'Task fetched', $task);
