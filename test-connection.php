<?php
// Simple connection test for external access
header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'message' => 'CHD Scholarship System is accessible',
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ]
]);
