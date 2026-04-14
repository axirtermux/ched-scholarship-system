<?php
require_once 'config.php';

$conn = getDBConnection();
$result = $conn->query('SELECT id, name, course, school FROM applicants LIMIT 5');

echo "Applicants in database:\n";
echo "========================\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}, Name: {$row['name']}, Course: {$row['course']}, School: {$row['school']}\n";
}
