<?php
require 'config.php';
require 'send_notification.php';

$complaint_id = intval($_POST['complaint_id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$assigned_staff = trim($_POST['assigned_staff'] ?? '');

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

$proof_photo_path = null;

// Handle proof photo upload
if (isset($_FILES['proof_photo']) && $_FILES['proof_photo']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/proofs/';

    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_extension = strtolower(pathinfo($_FILES['proof_photo']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($file_extension, $allowed_extensions)) {
        jsonResponse(false, 'Invalid file type. Only JPG, PNG, and GIF are allowed.', null, 400);
    }

    $new_filename = 'proof_' . $complaint_id . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $new_filename;

    if (move_uploaded_file($_FILES['proof_photo']['tmp_name'], $target_path)) {
        $proof_photo_path = $target_path;
    } else {
        jsonResponse(false, 'Failed to upload proof photo', null, 500);
    }
}

// Update complaint status, proof photo, and assigned staff
if ($proof_photo_path && !empty($assigned_staff)) {
    $stmt = $mysqli->prepare("UPDATE complaints SET status = ?, proof_photo = ?, assigned_staff = ? WHERE id = ?");
    $stmt->bind_param("sssi", $status, $proof_photo_path, $assigned_staff, $complaint_id);
} else if ($proof_photo_path) {
    $stmt = $mysqli->prepare("UPDATE complaints SET status = ?, proof_photo = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $proof_photo_path, $complaint_id);
} else if (!empty($assigned_staff)) {
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

    jsonResponse(true, 'Status updated successfully with proof');
} else {
    jsonResponse(false, 'Failed to update status', ["error" => $stmt->error], 500);
}
?>