<?php
require_once 'config.php';

$conn = getDBConnection();

// Check for triggers
$result = $conn->query("SHOW TRIGGERS LIKE 'applicants'");
echo "Triggers on applicants table:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}

// Check table structure with defaults
$result = $conn->query("SHOW FULL COLUMNS FROM applicants WHERE Field IN ('course', 'school')");
echo "\nColumn details for course and school:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
