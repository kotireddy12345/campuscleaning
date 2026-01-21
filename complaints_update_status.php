<?php
require 'config.php';
require 'send_notification.php';

$data = getJsonInput();
$complaint_id = intval($data['complaint_id'] ?? 0);
$status = trim($data['status'] ?? '');
$assigned_staff = trim($data['assigned_staff'] ?? '');

if ($complaint_id <= 0 || empty($status)) {
    jsonResponse(false, 'Complaint ID and status are required', null, 400);
}

// First, get the complaint details (user_id and title) before updating
$getStmt = $mysqli->prepare("SELECT user_id, title FROM complaints WHERE id = ?");
$getStmt->bind_param("i", $complaint_id);
$getStmt->execute();
$complaintResult = $getStmt->get_result();
$complaint = $complaintResult->fetch_assoc();
$getStmt->close();

if (!$complaint) {
    jsonResponse(false, 'Complaint not found', null, 404);
}

// Update with or without assigned_staff
if (!empty($assigned_staff)) {
    $stmt = $mysqli->prepare("UPDATE complaints SET status = ?, assigned_staff = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $assigned_staff, $complaint_id);
} else {
    $stmt = $mysqli->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $complaint_id);
}

if ($stmt->execute()) {
    // Send notification to the complaint owner
    $notificationTitle = "Complaint Status Updated";
    $statusLabel = ucfirst(str_replace('_', ' ', $status)); // "in_progress" -> "In Progress"
    $notificationMessage = "Your complaint \"{$complaint['title']}\" status has been updated to: {$statusLabel}";

    if (!empty($assigned_staff)) {
        $notificationMessage .= ". Assigned to: {$assigned_staff}";
    }

    sendNotificationToUser($mysqli, $complaint['user_id'], $notificationTitle, $notificationMessage);

    jsonResponse(true, 'Status updated successfully');
} else {
    jsonResponse(false, 'Failed to update status', ["error" => $stmt->error], 500);
}
?>