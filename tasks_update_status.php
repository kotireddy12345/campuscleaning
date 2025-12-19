<?php
require 'config.php';

$data = getJsonInput();

$task_id = $data['task_id'] ?? null;
$status  = $data['status']  ?? null; // pending / in_progress / completed

if ($task_id === null || $status === null) {
    jsonResponse(false, 'task_id and status are required', null, 400);
}

$stmt = $mysqli->prepare(
    "UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ?"
);
$stmt->bind_param('si', $status, $task_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    jsonResponse(true, 'Task status updated');
} else {
    jsonResponse(false, 'Task not found or not updated', null, 404);
}
