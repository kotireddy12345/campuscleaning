<?php
require 'config.php';

$sql = "
    SELECT 
        c.id, c.user_id, c.title, c.description, c.location, c.status, c.created_at, 
        c.photo_path, c.proof_photo, c.user_feedback, c.feedback_comment, c.assigned_staff,
        u.name AS reported_by_name
    FROM complaints c
    LEFT JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
";

$result = $mysqli->query($sql);
$data = [];
if ($result) {
    while ($row = $result->fetch_assoc())
        $data[] = $row;
}

jsonResponse(true, "Complaints fetched", $data);
?>