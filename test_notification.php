<?php
/**
 * Test FCM Notification Setup
 * 
 * Run this script to test if FCM notifications are working.
 * URL: http://your-server/campuscleaning/test_notification.php
 */

header("Content-Type: application/json");
require 'config.php';
require 'send_notification.php';

echo "<h2>FCM Notification Test</h2>";

// Step 1: Check if Service Account file exists
echo "<h3>Step 1: Service Account File</h3>";
$serviceAccountFile = __DIR__ . '/campuscleaning-1952d-firebase-adminsdk-fbsvc-71124d5682.json';
if (file_exists($serviceAccountFile)) {
    echo "✅ Service Account file exists<br>";
    $content = json_decode(file_get_contents($serviceAccountFile), true);
    if ($content && isset($content['client_email'])) {
        echo "✅ Client Email: " . $content['client_email'] . "<br>";
    } else {
        echo "❌ Service Account file is invalid<br>";
    }
} else {
    echo "❌ Service Account file NOT found at: $serviceAccountFile<br>";
}

// Step 2: Check if fcm_token column exists in users table
echo "<h3>Step 2: Database - fcm_token column</h3>";
$result = $mysqli->query("DESCRIBE users");
$hasFcmToken = false;
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'fcm_token') {
        $hasFcmToken = true;
        break;
    }
}
if ($hasFcmToken) {
    echo "✅ fcm_token column exists in users table<br>";
} else {
    echo "❌ fcm_token column NOT found! Run this SQL:<br>";
    echo "<code>ALTER TABLE users ADD COLUMN fcm_token VARCHAR(255) DEFAULT NULL;</code><br>";
}

// Step 3: Check if any managers have FCM tokens
echo "<h3>Step 3: Manager FCM Tokens</h3>";
$stmt = $mysqli->prepare("SELECT id, name, email, fcm_token FROM users WHERE role = 'manager'");
$stmt->execute();
$result = $stmt->get_result();

$managersWithToken = 0;
$managersTotal = 0;

while ($row = $result->fetch_assoc()) {
    $managersTotal++;
    echo "Manager: " . $row['name'] . " (" . $row['email'] . ")<br>";
    if (!empty($row['fcm_token'])) {
        $managersWithToken++;
        echo "✅ Has FCM token: " . substr($row['fcm_token'], 0, 30) . "...<br>";
    } else {
        echo "❌ NO FCM token! Manager needs to login again to register token.<br>";
    }
    echo "<br>";
}

if ($managersTotal == 0) {
    echo "❌ No managers found in database!<br>";
} else {
    echo "Total managers: $managersTotal, With FCM token: $managersWithToken<br>";
}

// Step 4: Test getting OAuth Access Token
echo "<h3>Step 4: OAuth Access Token Test</h3>";
$accessToken = getAccessToken();
if ($accessToken) {
    echo "✅ Access token obtained successfully!<br>";
    echo "Token (first 50 chars): " . substr($accessToken, 0, 50) . "...<br>";
} else {
    echo "❌ Failed to get access token! Check Service Account file and PHP OpenSSL extension.<br>";
}

// Step 5: Test sending notification (if there are managers with tokens)
if ($managersWithToken > 0 && $accessToken) {
    echo "<h3>Step 5: Test Send Notification</h3>";
    $testResult = sendNotificationToManagers($mysqli, "Test Notification", "This is a test from the server!", 0);
    if ($testResult) {
        echo "✅ Test notification sent! Check manager device.<br>";
    } else {
        echo "❌ Failed to send notification. Check PHP error logs for details.<br>";
    }
} else {
    echo "<h3>Step 5: Skipped</h3>";
    echo "Cannot test - either no managers with tokens or access token failed.<br>";
}

echo "<br><br><strong>Check PHP error logs for more details!</strong>";
?>