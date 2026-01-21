<?php
require 'config.php';
require 'send_notification.php';

// Custom debug log function
function debugLog($message)
{
    $logFile = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Log everything for debugging
debugLog("========================================");
debugLog("COMPLAINT CREATE WITH PHOTO");
debugLog("========================================");

$user_id = intval($_POST['user_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$location = trim($_POST['location'] ?? '');

debugLog("User ID: $user_id");
debugLog("Title: $title");
debugLog("Description: $description");
debugLog("Location: $location");

if ($user_id <= 0 || empty($title) || empty($description) || empty($location)) {
    debugLog("ERROR: Missing required fields");
    jsonResponse(false, "user_id, title, description, and location are required", null, 400);
}

$photoPath = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = "complaint_" . time() . "_" . basename($_FILES["photo"]["name"]);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        $photoPath = "uploads/" . $fileName;
        debugLog("Photo uploaded: $photoPath");
    }
}

$stmt = $mysqli->prepare("INSERT INTO complaints (user_id, title, description, location, photo_path, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("issss", $user_id, $title, $description, $location, $photoPath);

if ($stmt->execute()) {
    $complaintId = $mysqli->insert_id;
    debugLog("Complaint created! ID: $complaintId");

    // Send notification to all managers
    $notificationTitle = "New Complaint Submitted";
    $notificationMessage = "New complaint: " . $title;
    if (!empty($location)) {
        $notificationMessage .= " at " . $location;
    }

    debugLog("Sending notification to managers...");
    debugLog("Title: $notificationTitle");
    debugLog("Message: $notificationMessage");

    // Get all manager tokens first for logging
    $tokenStmt = $mysqli->prepare("SELECT id, name, email, fcm_token FROM users WHERE role = 'manager' AND id != ?");
    $tokenStmt->bind_param("i", $user_id);
    $tokenStmt->execute();
    $tokenResult = $tokenStmt->get_result();

    debugLog("Manager tokens:");
    $managerCount = 0;
    while ($row = $tokenResult->fetch_assoc()) {
        $managerCount++;
        $tokenStatus = empty($row['fcm_token']) ? "NULL (no token!)" : "HAS TOKEN: " . substr($row['fcm_token'], 0, 30) . "...";
        debugLog("  - {$row['name']} ({$row['email']}): $tokenStatus");
    }
    debugLog("Total managers found: $managerCount");
    $tokenStmt->close();

    // Now send notifications
    $notifyResult = sendNotificationToManagers($mysqli, $notificationTitle, $notificationMessage, $user_id);
    debugLog("Notification result: " . ($notifyResult ? "SUCCESS" : "FAILED"));
    debugLog("========================================");

    jsonResponse(true, "Complaint created successfully with photo");
} else {
    debugLog("ERROR: Failed to create complaint - " . $stmt->error);
    jsonResponse(false, "Failed to create complaint", ["error" => $stmt->error], 500);
}
?>