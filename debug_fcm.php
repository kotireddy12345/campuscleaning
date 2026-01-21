<?php
/**
 * Debug FCM Token Setup
 * 
 * This script helps debug FCM token issues.
 * Run in browser: http://localhost/campusclean_api/debug_fcm.php
 */

header("Content-Type: text/html; charset=UTF-8");
require 'config.php';

echo "<h1>🔧 FCM Debug Tool</h1>";

// Step 1: Check fcm_token column
echo "<h2>Step 1: Check fcm_token column</h2>";
$result = $mysqli->query("DESCRIBE users");
$hasFcmToken = false;
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'fcm_token') {
        $hasFcmToken = true;
        echo "✅ fcm_token column exists!<br>";
        break;
    }
}
if (!$hasFcmToken) {
    echo "❌ fcm_token column NOT found! Running ALTER TABLE...<br>";
    $mysqli->query("ALTER TABLE users ADD COLUMN fcm_token VARCHAR(255) DEFAULT NULL");
    echo "✅ Column added! Refresh page to verify.<br>";
}

// Step 2: List all users with their FCM tokens
echo "<h2>Step 2: Users and FCM Tokens</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>FCM Token</th></tr>";

$result = $mysqli->query("SELECT id, name, email, role, fcm_token FROM users");
while ($row = $result->fetch_assoc()) {
    $tokenDisplay = empty($row['fcm_token']) ? "❌ NULL" : "✅ " . substr($row['fcm_token'], 0, 40) . "...";
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$row['role']}</td>";
    echo "<td style='font-size:12px;'>{$tokenDisplay}</td>";
    echo "</tr>";
}
echo "</table>";

// Step 3: Count managers with tokens
echo "<h2>Step 3: Manager Token Status</h2>";
$stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'manager'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalManagers = $row['total'];

$stmt = $mysqli->prepare("SELECT COUNT(*) as withToken FROM users WHERE role = 'manager' AND fcm_token IS NOT NULL AND fcm_token != ''");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$managersWithToken = $row['withToken'];

echo "Total Managers: {$totalManagers}<br>";
echo "Managers with FCM Token: {$managersWithToken}<br>";

if ($managersWithToken == 0) {
    echo "<br>⚠️ <strong>No managers have FCM tokens! They need to logout and login again in the app.</strong><br>";
}

// Step 4: Test manual token update
echo "<h2>Step 4: Manual Token Test</h2>";
if (isset($_GET['test_user_id']) && isset($_GET['test_token'])) {
    $testUserId = intval($_GET['test_user_id']);
    $testToken = $_GET['test_token'];

    $stmt = $mysqli->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
    $stmt->bind_param("si", $testToken, $testUserId);

    if ($stmt->execute()) {
        echo "✅ Token updated for user ID: {$testUserId}<br>";
    } else {
        echo "❌ Failed: " . $stmt->error . "<br>";
    }
} else {
    echo "To manually test, add: ?test_user_id=1&test_token=test123<br>";
}

// Step 5: Check service account file
echo "<h2>Step 5: Firebase Service Account</h2>";
$serviceAccountFile = __DIR__ . '/campuscleaning-1952d-firebase-adminsdk-fbsvc-71124d5682.json';
if (file_exists($serviceAccountFile)) {
    echo "✅ Service account file exists<br>";
    $content = json_decode(file_get_contents($serviceAccountFile), true);
    if ($content && isset($content['client_email'])) {
        echo "✅ Client Email: " . $content['client_email'] . "<br>";
    }
} else {
    echo "❌ Service account file NOT found at: {$serviceAccountFile}<br>";
}

echo "<br><hr>";
echo "<h3>🔄 Instructions:</h3>";
echo "<ol>";
echo "<li>Make sure this file is in your server's campusclean_api folder</li>";
echo "<li>Have managers <strong>logout and login again</strong> in the app</li>";
echo "<li>Refresh this page to see if FCM tokens appear</li>";
echo "<li>Then test creating a complaint</li>";
echo "</ol>";

$mysqli->close();
?>