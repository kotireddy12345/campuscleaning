<?php
require 'config.php';

$status = $_GET['status'] ?? null; // open/in_progress/resolved

$query = "SELECT c.id, c.title, c.description, c.priority, c.status,
                 c.created_at,
                 b.name AS building_name,
                 r.name AS room_name
          FROM complaints c
          LEFT JOIN buildings b ON c.building_id = b.id
          LEFT JOIN rooms r ON c.room_id = r.id
          WHERE 1=1";

$params = [];
$types  = '';

if ($status !== null) {
    $query .= " AND c.status = ?";
    $types  .= 's';
    $params[] = $status;
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $mysqli->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

jsonResponse(true, 'Complaints fetched', $complaints);
