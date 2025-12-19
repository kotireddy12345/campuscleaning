<?php
require 'config.php';

$data = getJsonInput();

$title       = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$building_id = $data['building_id'] ?? null;
$room_id     = $data['room_id'] ?? null;
$category    = $data['category'] ?? null;
$priority    = $data['priority'] ?? 'medium';
$reported_by = $data['reported_by'] ?? null; // user id (student/staff)

if ($title === '' || $reported_by === null) {
    jsonResponse(false, 'title and reported_by are required', null, 400);
}

$stmt = $mysqli->prepare(
    "INSERT INTO complaints
     (title, description, building_id, room_id, category, priority, reported_by)
     VALUES (?,?,?,?,?,?,?)"
);

$stmt->bind_param(
    'ssiissi',
    $title,
    $description,
    $building_id,
    $room_id,
    $category,
    $priority,
    $reported_by
);

if ($stmt->execute()) {
    jsonResponse(true, 'Complaint created successfully', [
        'complaint_id' => $stmt->insert_id
    ], 201);
} else {
    jsonResponse(false, 'Failed to create complaint', null, 500);
}
