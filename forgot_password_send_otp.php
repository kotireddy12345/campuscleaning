<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'config.php';

// Set timezone to match MySQL server
date_default_timezone_set('Asia/Kolkata');

// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'peramkotireddy12345@gmail.com');
define('SMTP_PASS', 'rkfnmapvimfulkoe'); // App password without spaces

function sendEmailViaSMTP($to, $subject, $htmlBody)
{
    $from = SMTP_USER;
    $fromName = "Campus Cleaning";

    // Create email headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $fromName <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";

    // Try using PHP mail with ini settings
    ini_set('SMTP', SMTP_HOST);
    ini_set('smtp_port', SMTP_PORT);
    ini_set('sendmail_from', $from);

    // Use PEAR Mail or fsockopen for SMTP auth
    $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 30);
    if (!$socket) {
        return false;
    }

    // Read greeting
    fgets($socket, 512);

    // Say hello
    fputs($socket, "EHLO localhost\r\n");
    while ($line = fgets($socket, 512)) {
        if (substr($line, 3, 1) == ' ')
            break;
    }

    // Start TLS
    fputs($socket, "STARTTLS\r\n");
    fgets($socket, 512);

    // Enable crypto
    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

    // Say hello again after TLS
    fputs($socket, "EHLO localhost\r\n");
    while ($line = fgets($socket, 512)) {
        if (substr($line, 3, 1) == ' ')
            break;
    }

    // Authenticate
    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 512);

    fputs($socket, base64_encode(SMTP_USER) . "\r\n");
    fgets($socket, 512);

    fputs($socket, base64_encode(SMTP_PASS) . "\r\n");
    $authResponse = fgets($socket, 512);

    if (substr($authResponse, 0, 3) != '235') {
        fclose($socket);
        return false;
    }

    // Send email
    fputs($socket, "MAIL FROM:<$from>\r\n");
    fgets($socket, 512);

    fputs($socket, "RCPT TO:<$to>\r\n");
    fgets($socket, 512);

    fputs($socket, "DATA\r\n");
    fgets($socket, 512);

    // Email content
    $message = "To: $to\r\n";
    $message .= "From: $fromName <$from>\r\n";
    $message .= "Subject: $subject\r\n";
    $message .= $headers;
    $message .= "\r\n" . $htmlBody . "\r\n.\r\n";

    fputs($socket, $message);
    fgets($socket, 512);

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return true;
}

function sendOtpEmail($to_email, $otp, $user_name = "User")
{
    $subject = "Campus Cleaning - Password Reset OTP";

    $htmlBody = "
    <html>
    <body style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px;'>
        <h2 style='color: #6200EE;'>Password Reset Request</h2>
        <p>Hello <strong>$user_name</strong>,</p>
        <p>You have requested to reset your password. Please use the following OTP:</p>
        <div style='font-size: 32px; font-weight: bold; color: #6200EE; letter-spacing: 5px; text-align: center; padding: 20px; background: #f5f5f5; border-radius: 8px; margin: 20px 0;'>
            $otp
        </div>
        <p>This OTP is valid for <strong>10 minutes</strong>.</p>
        <p style='color: #888; font-size: 12px;'>If you did not request this, please ignore this email.</p>
        <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
        <p style='color: #888; font-size: 12px;'>Campus Cleaning Management System</p>
    </body>
    </html>
    ";

    return sendEmailViaSMTP($to_email, $subject, $htmlBody);
}

// Main logic
$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->email)) {
    jsonResponse(false, "Email is required");
}

$email = trim($data->email);

// Check if email exists
$stmt = $mysqli->prepare("SELECT id, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    jsonResponse(false, "Email not found");
}

$user = $result->fetch_assoc();
$user_name = $user['name'];

// Generate 6-digit OTP
$otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Delete any existing OTP for this email
$deleteStmt = $mysqli->prepare("DELETE FROM password_resets WHERE email = ?");
$deleteStmt->bind_param("s", $email);
$deleteStmt->execute();

// Store OTP in database - use MySQL NOW() to ensure timezone consistency
$insertStmt = $mysqli->prepare("INSERT INTO password_resets (email, otp, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
$insertStmt->bind_param("ss", $email, $otp);

if ($insertStmt->execute()) {
    // Send email
    $email_sent = sendOtpEmail($email, $otp, $user_name);

    if ($email_sent) {
        jsonResponse(true, "OTP sent to your email");
    } else {
        // Email failed, return OTP for testing
        jsonResponse(true, "Email delivery issue - use this OTP", ["otp" => $otp]);
    }
} else {
    jsonResponse(false, "Failed to generate OTP");
}

$stmt->close();
$mysqli->close();
?>