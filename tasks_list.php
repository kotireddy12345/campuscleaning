<?php
require 'config.php';

// Optional filters: status, assigned_to, date
$status      = $_GET['status']      ?? null; // pending / in_progress / completed
$assigned_to = $_GET['assigned_to'] ?? null; // staff user id
$date        = $_GET['date']        ?? null; // "2025-11-20"

$query = "SELECT t.id, t.title, t.description, t.scheduled_date, t.start_time,
                 t.end_time, t.priority, t.status,
                 b.name AS building_name,
                 r.name AS room_name
          FROM tasks t
          LEFT JOIN buildings b ON t.building_id = b.id
          LEFT JOIN rooms r ON t.room_id = r.id
          WHERE 1=1";

$params = [];
$types  = '';

if ($status !== null) {
    $query .= " AND t.status = ?";
    $types  .= 's';
    $params[] = $status;
}

if ($assigned_to !== null) {
    $query .= " AND t.assigned_to = ?";
    $types  .= 'i';
    $params[] = (int)$assigned_to;
}

if ($date !== null) {
    $query .= " AND t.scheduled_date = ?";
    $types  .= 's';
    $params[] = $date;
}

$query .= " ORDER BY t.scheduled_date, t.start_time";

$stmt = $mysqli->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

jsonResponse(true, 'Tasks fetched', $tasks);
