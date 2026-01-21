<?php
require 'config.php';

$data = getJsonInput();
$complaint_id = intval($data['complaint_id'] ?? 0);
$user_id = intval($data['user_id'] ?? 0);
$feedback = trim($data['feedback'] ?? '');
$comment = trim($data['comment'] ?? ''); // Optional user comment

// Validate required fields
if ($complaint_id <= 0 || $user_id <= 0 || empty($feedback)) {
    jsonResponse(false, 'Complaint ID, User ID, and feedback are required', null, 400);
}

// Validate feedback value
if (!in_array($feedback, ['ok', 'not_ok'])) {
    jsonResponse(false, 'Invalid feedback value. Must be "ok" or "not_ok"', null, 400);
}

// Validate comment length (max 500 characters)
if (strlen($comment) > 500) {
    jsonResponse(false, 'Comment is too long. Maximum 500 characters allowed.', null, 400);
}

// Verify that the complaint belongs to this user and is completed
$checkStmt = $mysqli->prepare("SELECT status FROM complaints WHERE id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $complaint_id, $user_id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$complaint = $result->fetch_assoc();
$checkStmt->close();

if (!$complaint) {
    jsonResponse(false, 'Complaint not found or does not belong to this user', null, 404);
}

if ($complaint['status'] !== 'completed') {
    jsonResponse(false, 'Feedback can only be submitted for completed complaints', null, 400);
}

// Update the feedback and comment
$stmt = $mysqli->prepare("UPDATE complaints SET user_feedback = ?, feedback_comment = ? WHERE id = ? AND user_id = ?");
$commentToSave = empty($comment) ? null : $comment;
$stmt->bind_param("ssii", $feedback, $commentToSave, $complaint_id, $user_id);

if ($stmt->execute()) {
    jsonResponse(true, 'Feedback submitted successfully');
} else {
    jsonResponse(false, 'Failed to submit feedback', ["error" => $stmt->error], 500);
}
?>