<?php
require_once 'config.php';

$conn = getDBConnection();

// Clear the table
$conn->query("DELETE FROM applicants");

// Test with explicit variables
$id = 'TEST002';
$name = 'Test User';
$dob = '2002-01-01';
$gender = 'Male';
$contact = '09171111111';
$email = 'test@email.com';
$address = 'Test Address';
$course = 'BS Computer Science';
$year = 2;
$school = 'ESSU Main';
$gwa = 1.50;
$annual_income = 120000.00;
$status = 'Pending';
$documents = '[]';
$remarks = '';
$avatar = 'TU';

$stmt = $conn->prepare("INSERT INTO applicants (id, name, dob, gender, contact, email, address, course, year, school, gwa, annual_income, status, documents, submitted_date, remarks, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?)");

// Try individual bind_param calls
$stmt->bind_param('s', $id);
$stmt->bind_param('s', $name);
$stmt->bind_param('s', $dob);
$stmt->bind_param('s', $gender);
$stmt->bind_param('s', $contact);
$stmt->bind_param('s', $email);
$stmt->bind_param('s', $address);
$stmt->bind_param('s', $course);
$stmt->bind_param('i', $year);
$stmt->bind_param('s', $school);
$stmt->bind_param('d', $gwa);
$stmt->bind_param('d', $annual_income);
$stmt->bind_param('s', $status);
$stmt->bind_param('s', $documents);
$stmt->bind_param('s', $remarks);
$stmt->bind_param('s', $avatar);

if ($stmt->execute()) {
    echo "Prepared statement insert successful\n";
} else {
    echo "Prepared statement insert failed: " . $stmt->error . "\n";
}
$stmt->close();

// Verify
$result = $conn->query("SELECT id, name, course, school FROM applicants WHERE id = 'TEST002'");
$row = $result->fetch_assoc();
echo "Verification: ID={$row['id']}, Name={$row['name']}, Course={$row['course']}, School={$row['school']}\n";
