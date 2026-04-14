<?php
require_once 'config.php';

$conn = getDBConnection();

// Update existing applicants with correct course and school values
$updates = [
    ['id' => 'A0278c1e4', 'name' => 'Sofia Martinez', 'course' => 'BS Accountancy', 'school' => 'ESSU Main'],
    ['id' => 'A04e9558c', 'name' => 'Elena Rodriguez', 'course' => 'BS Social Work', 'school' => 'ESSU Can-avid'],
    ['id' => 'A2f293dcd', 'name' => 'Carlos Lopez', 'course' => 'BS Psychology', 'school' => 'ESSU Main'],
    ['id' => 'A33c8ee57', 'name' => 'Jose Rizal', 'course' => 'BS Engineering', 'school' => 'ESSU Main'],
    ['id' => 'A3f05442c', 'name' => 'Miguel Torres', 'course' => 'BS Mathematics', 'school' => 'ESSU Sta. Rita'],
];

foreach ($updates as $update) {
    $sql = "UPDATE applicants SET course = '{$update['course']}', school = '{$update['school']}' WHERE id = '{$update['id']}'";
    if ($conn->query($sql)) {
        echo "Updated: {$update['name']} - Course: {$update['course']}, School: {$update['school']}\n";
    } else {
        echo "Failed to update {$update['name']}: " . $conn->error . "\n";
    }
}

// Get remaining applicants and update them
$result = $conn->query("SELECT id, name FROM applicants WHERE course = '0' OR course IS NULL");
while ($row = $result->fetch_assoc()) {
    // Assign random course and school from the lists
    $courses = ['BS Education', 'BS Nursing', 'BS Engineering', 'BS IT', 'BS Agriculture', 'BS Accountancy', 'BS Mathematics', 'BS Biology', 'BS Psychology', 'BS Social Work'];
    $schools = ['ESSU Main', 'ESSU Borongan', 'ESSU Salcedo', 'ESSU Taft', 'ESSU Sta. Rita', 'ESSU Can-avid'];
    
    $randomCourse = $courses[array_rand($courses)];
    $randomSchool = $schools[array_rand($schools)];
    
    $sql = "UPDATE applicants SET course = '$randomCourse', school = '$randomSchool' WHERE id = '{$row['id']}'";
    if ($conn->query($sql)) {
        echo "Updated: {$row['name']} - Course: $randomCourse, School: $randomSchool\n";
    }
}

echo "\nVerification:\n";
$result = $conn->query('SELECT id, name, course, school FROM applicants LIMIT 5');
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}, Name: {$row['name']}, Course: {$row['course']}, School: {$row['school']}\n";
}
