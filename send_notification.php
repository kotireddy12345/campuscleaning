<?php
/**
 * Send FCM Notification Helper - FCM HTTP v1 API
 * 
 * This file uses the modern FCM HTTP v1 API with Service Account authentication.
 * Place your Firebase Service Account JSON file in the same directory.
 */

// ============================================
// SERVICE ACCOUNT JSON FILE NAME
// ============================================
define('SERVICE_ACCOUNT_FILE', __DIR__ . '/campuscleaning-1952d-firebase-adminsdk-fbsvc-71124d5682.json');
define('FCM_PROJECT_ID', 'campuscleaning-1952d');
// ============================================

/**
 * Get OAuth 2.0 access token from Service Account
 */
function getAccessToken()
{
    if (!file_exists(SERVICE_ACCOUNT_FILE)) {
        error_log("FCM Error: Service account file not found: " . SERVICE_ACCOUNT_FILE);
        return null;
    }

    $serviceAccount = json_decode(file_get_contents(SERVICE_ACCOUNT_FILE), true);

    if (!$serviceAccount) {
        error_log("FCM Error: Invalid service account JSON");
        return null;
    }

    // Create JWT
    $now = time();
    $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'RS256']));
    $payload = base64_encode(json_encode([
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600
    ]));

    // Sign JWT
    $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
    $signature = '';
    openssl_sign("$header.$payload", $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $jwt = "$header.$payload." . base64_encode($signature);

    // Exchange JWT for access token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return isset($data['access_token']) ? $data['access_token'] : null;
}

/**
 * Send notification to managers when a new complaint is created
 */
function sendNotificationToManagers($mysqli, $title, $message, $excludeUserId = 0)
{
    // Get all manager FCM tokens
    $stmt = $mysqli->prepare("SELECT fcm_token FROM users WHERE role = 'manager' AND fcm_token IS NOT NULL AND fcm_token != '' AND id != ?");
    $stmt->bind_param("i", $excludeUserId);
    $stmt->execute();
    $result = $stmt->get_result();

    $tokens = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['fcm_token'])) {
            $tokens[] = $row['fcm_token'];
        }
    }
    $stmt->close();

    if (empty($tokens)) {
        error_log("FCM: No manager tokens found");
        return false;
    }

    $success = false;
    foreach ($tokens as $token) {
        if (sendFcmNotification($token, $title, $message)) {
            $success = true;
        }
    }

    return $success;
}

/**
 * Send notification to a specific user when their complaint status is updated
 */
function sendNotificationToUser($mysqli, $userId, $title, $message)
{
    // Get user's FCM token
    $stmt = $mysqli->prepare("SELECT fcm_token FROM users WHERE id = ? AND fcm_token IS NOT NULL AND fcm_token != ''");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $token = $row['fcm_token'];
        $stmt->close();

        if (!empty($token)) {
            error_log("FCM: Sending notification to user ID $userId with token: " . substr($token, 0, 20) . "...");
            return sendFcmNotification($token, $title, $message);
        }
    }

    $stmt->close();
    error_log("FCM: No FCM token found for user ID $userId");
    return false;
}

/**
 * Send FCM notification to a single device token using HTTP v1 API
 */
function sendFcmNotification($token, $title, $message)
{
    $accessToken = getAccessToken();

    if (!$accessToken) {
        error_log("FCM Error: Could not get access token");
        return false;
    }

    $url = 'https://fcm.googleapis.com/v1/projects/' . FCM_PROJECT_ID . '/messages:send';

    $data = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $message
            ],
            'data' => [
                'title' => $title,
                'message' => $message
            ],
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    'click_action' => 'OPEN_ACTIVITY'
                ]
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("FCM curl error: " . $error);
        return false;
    }

    error_log("FCM Response (HTTP $httpCode): " . $response);

    return $httpCode == 200;
}
?>