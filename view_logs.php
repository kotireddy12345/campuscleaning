<?php
/**
 * View Debug Logs
 * Access via: http://localhost/campusclean_api/view_logs.php
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<!DOCTYPE html><html><head><title>Debug Logs</title>
<style>
body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
h1 { color: #569cd6; }
h2 { color: #4ec9b0; }
pre { background: #2d2d2d; padding: 15px; border-radius: 5px; overflow-x: auto; white-space: pre-wrap; max-height: 500px; overflow-y: auto; }
.highlight { color: #4ec9b0; }
.error { color: #f14c4c; }
.success { color: #4ec9b0; }
.refresh { background: #0e639c; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; margin-bottom: 20px; }
</style>
</head><body>";

echo "<h1>📋 Debug Logs</h1>";
echo "<button class='refresh' onclick='location.reload()'>🔄 Refresh Logs</button>";

// Try multiple log file locations
$logFiles = [
    __DIR__ . '/debug.log',  // Custom log in current folder
    'C:\\xampp\\apache\\logs\\error.log',
];

$foundLog = false;

foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        $foundLog = true;
        echo "<h2>Log: " . basename($logFile) . "</h2>";

        // Get last 150 lines
        $lines = file($logFile);
        $lastLines = array_slice($lines, -150);

        echo "<pre>";
        foreach ($lastLines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'FAILED') !== false || strpos($line, 'error') !== false) {
                echo "<span class='error'>" . htmlspecialchars($line) . "</span>";
            } elseif (strpos($line, 'SUCCESS') !== false || strpos($line, 'successfully') !== false) {
                echo "<span class='success'>" . htmlspecialchars($line) . "</span>";
            } elseif (strpos($line, 'COMPLAINT') !== false || strpos($line, 'FCM') !== false || strpos($line, 'Manager') !== false || strpos($line, 'notification') !== false) {
                echo "<span class='highlight'>" . htmlspecialchars($line) . "</span>";
            } else {
                echo htmlspecialchars($line);
            }
        }
        echo "</pre>";
    }
}

if (!$foundLog) {
    echo "<p class='error'>No log files found!</p>";
    echo "<p>Creating a test entry in debug.log...</p>";

    // Create test log
    $debugLog = __DIR__ . '/debug.log';
    file_put_contents($debugLog, date('Y-m-d H:i:s') . " - Debug log initialized\n");
    echo "<p class='success'>Created debug.log - Refresh page to see logs</p>";
}

echo "</body></html>";
?>