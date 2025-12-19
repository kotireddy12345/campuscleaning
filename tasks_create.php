<?php
require 'config.php';

$data = getJsonInput();

$title         = trim($data['title'] ?? '');
$description   = trim($data['description'] ?? '');
$building_id   = $data['building_id'] ?? null;
$room_id       = $data['room_id'] ?? null;
$scheduledDate = $data['scheduled_date'] ?? null; // "2025-11-20"
$startTime     = $data['start_time'] ?? null;     // "09:00:00"
$endTime       = $data['end_time'] ?? null;       // "10:30:00"
$priority      = $data['priority'] ?? 'medium';   // low/medium/high
$assigned_to   = $data['assigned_to'] ?? null;    // staff user id
$created_by    = $data['created_by'] ?? null;     // manager user id

if ($title === '') {
    jsonResponse(false, 'Task title is required', null, 400);
}

$stmt = $mysqli->prepare(
    "INSERT INTO tasks
     (title, description, building_id, room_id, scheduled_date, start_time, end_time, priority, assigned_to, created_by)
     VALUES (?,?,?,?,?,?,?,?,?,?)"
);

$stmt->bind_param(
    'ssisssssii',
    $title,
    $description,
    $building_id,
    $room_id,
    $scheduledDate,
    $startTime,
    $endTime,
    $priority,
    $assigned_to,
    $created_by
);

if ($stmt->execute()) {
    jsonResponse(true, 'Task created successfully', [
        'task_id' => $stmt->insert_id
    ], 201);
} else {
    jsonResponse(false, 'Failed to create task: ' . $stmt->error, null, 500);
}
