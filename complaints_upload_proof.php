<?php
require 'config.php';

$complaint_id = intval($_POST['complaint_id'] ?? 0);

if ($complaint_id <= 0) {
    jsonResponse(false, "Complaint ID is required", null, 400);
}

$proofPath = null;
if (isset($_FILES['proof_photo']) && $_FILES['proof_photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = "proof_" . time() . "_" . basename($_FILES["proof_photo"]["name"]);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["proof_photo"]["tmp_name"], $targetFile)) {
        $proofPath = "uploads/" . $fileName;
    }
}

if ($proofPath) {
    $stmt = $mysqli->prepare("UPDATE complaints SET proof_photo = ? WHERE id = ?");
    $stmt->bind_param("si", $proofPath, $complaint_id);

    if ($stmt->execute()) {
        jsonResponse(true, "Proof uploaded successfully");
    } else {
        jsonResponse(false, "Failed to update proof path", ["error" => $stmt->error], 500);
    }
} else {
    jsonResponse(false, "File upload failed", null, 500);
}
?>