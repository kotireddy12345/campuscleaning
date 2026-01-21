<?php
require 'config.php';

$data = getJsonInput();
$complaint_id = intval($data['complaint_id'] ?? 0);

if ($complaint_id <= 0) {
    jsonResponse(false, 'Complaint ID is required', null, 400);
}

$stmt = $mysqli->prepare("DELETE FROM complaints WHERE id = ?");
$stmt->bind_param("i", $complaint_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        jsonResponse(true, 'Complaint deleted successfully');
    } else {
        jsonResponse(false, 'Complaint not found or already deleted', null, 404);
    }
} else {
    jsonResponse(false, 'Failed to delete complaint', ["error" => $stmt->error], 500);
}
?>