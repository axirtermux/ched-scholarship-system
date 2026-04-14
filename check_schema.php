<?php
require_once 'config.php';

$conn = getDBConnection();
$result = $conn->query("DESCRIBE applicants");

echo "Applicants table schema:\n";
echo "========================\n";
while ($row = $result->fetch_assoc()) {
    echo "Field: {$row['Field']}, Type: {$row['Type']}, Null: {$row['Null']}, Default: {$row['Default']}\n";
}
