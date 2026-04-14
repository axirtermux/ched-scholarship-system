<?php
require_once 'config.php';

$conn = getDBConnection();

// Clear the table
$conn->query("DELETE FROM applicants");

// Try direct SQL insert without prepared statement
$sql = "INSERT INTO applicants (id, name, dob, gender, contact, email, address, course, year, school, gwa, annual_income, status, documents, submitted_date, remarks, avatar) 
VALUES ('TEST001', 'Test User', '2002-01-01', 'Male', '09171111111', 'test@email.com', 'Test Address', 'BS Computer Science', 2, 'ESSU Main', 1.50, 120000.00, 'Pending', '[]', CURDATE(), '', 'TU')";

if ($conn->query($sql)) {
    echo "Direct insert successful\n";
} else {
    echo "Direct insert failed: " . $conn->error . "\n";
}

// Verify
$result = $conn->query("SELECT id, name, course, school FROM applicants WHERE id = 'TEST001'");
$row = $result->fetch_assoc();
echo "Verification: ID={$row['id']}, Name={$row['name']}, Course={$row['course']}, School={$row['school']}\n";
