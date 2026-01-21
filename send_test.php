<?php
/**
 * Simple Notification Sender Test
 * Access via: http://localhost/campusclean_api/send_test.php?user_id=3
 * 
 * This will send a test notification to the specified user ID
 */

// Include config first (it sets JSON header)
require 'config.php';
require 'send_notification.php';

// Override with HTML header - MUST be after config.php include
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>📱 Send Test Notification</h1>";

// Get user_id from query or default to first manager
$targetUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($targetUserId > 0) {
    // Get user info and token
    $stmt = $mysqli->prepare("SELECT id, name, email, role, fcm_token FROM users WHERE id = ?");
    $stmt->bind_param("i", $targetUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo "<h2>Target User:</h2>";
        echo "<ul>";
        echo "<li>ID: {$user['id']}</li>";
        echo "<li>Name: {$user['name']}</li>";
        echo "<li>Email: {$user['email']}</li>";
        echo "<li>Role: {$user['role']}</li>";
        echo "<li>FCM Token: " . (empty($user['fcm_token']) ? "❌ NULL" : "✅ " . substr($user['fcm_token'], 0, 50) . "...") . "</li>";
        echo "</ul>";

        if (!empty($user['fcm_token'])) {
            echo "<h2>Sending notification...</h2>";

            // Try sending notification
            $title = "Test Notification";
            $message = "This is a test sent at " . date('H:i:s');

            $sendResult = sendFcmNotification($user['fcm_token'], $title, $message);

            if ($sendResult) {
                echo "✅ <strong>Notification sent successfully!</strong><br>";
                echo "Check your phone for the notification.<br>";
            } else {
                echo "❌ <strong>Failed to send notification!</strong><br>";
                echo "Check PHP error logs for details.<br>";
            }
        } else {
            echo "❌ Cannot send - user has no FCM token!<br>";
        }
    } else {
        echo "❌ User ID $targetUserId not found!<br>";
    }
} else {
    echo "<h2>Choose a user to send test notification:</h2>";
    echo "<ul>";

    $result = $mysqli->query("SELECT id, name, email, role, fcm_token FROM users WHERE fcm_token IS NOT NULL AND fcm_token != ''");
    while ($row = $result->fetch_assoc()) {
        $tokenStatus = empty($row['fcm_token']) ? "❌ No token" : "✅ Has token";
        echo "<li><a href='?user_id={$row['id']}'>{$row['name']}</a> ({$row['email']}) - {$row['role']} - $tokenStatus</li>";
    }

    echo "</ul>";

    if ($result->num_rows == 0) {
        echo "❌ No users have FCM tokens! They need to login in the app first.";
    }
}

echo "<hr>";
echo "<h3>PHP Error Log (last notification-related entries):</h3>";
echo "<p>Check your XAMPP logs at: C:\\xampp\\php\\logs\\php_error_log</p>";

$mysqli->close();
?>