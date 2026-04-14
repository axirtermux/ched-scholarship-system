<?php
require_once 'config.php';

$conn = getDBConnection();

// Clear existing applicants
$conn->query("DELETE FROM applicants");
echo "Cleared applicants table\n";

// Re-insert applicant data with proper course and school
$applicantData = [
    ['name' => 'Juan Dela Cruz', 'dob' => '2002-05-15', 'gender' => 'Male', 'contact' => '09171111111', 'email' => 'juan1@email.com', 'address' => 'Borongan City, Eastern Samar', 'course' => 'BS Education', 'year' => 2, 'school' => 'ESSU Main', 'gwa' => 1.25, 'annual_income' => 120000, 'status' => 'Pending'],
    ['name' => 'Maria Santos', 'dob' => '2003-03-20', 'gender' => 'Female', 'contact' => '09171111112', 'email' => 'maria1@email.com', 'address' => 'Tacloban City, Leyte', 'course' => 'BS Nursing', 'year' => 1, 'school' => 'ESSU Borongan', 'gwa' => 1.50, 'annual_income' => 150000, 'status' => 'Under Review'],
    ['name' => 'Jose Rizal', 'dob' => '2002-08-10', 'gender' => 'Male', 'contact' => '09171111113', 'email' => 'jose1@email.com', 'address' => 'Catarman, Northern Samar', 'course' => 'BS Engineering', 'year' => 3, 'school' => 'ESSU Main', 'gwa' => 1.35, 'annual_income' => 95000, 'status' => 'Pending'],
    ['name' => 'Ana Garcia', 'dob' => '2003-01-25', 'gender' => 'Female', 'contact' => '09171111114', 'email' => 'ana1@email.com', 'address' => 'Calbayog City, Samar', 'course' => 'BS IT', 'year' => 2, 'school' => 'ESSU Salcedo', 'gwa' => 1.45, 'annual_income' => 180000, 'status' => 'Approved'],
    ['name' => 'Pedro Reyes', 'dob' => '2002-11-30', 'gender' => 'Male', 'contact' => '09171111115', 'email' => 'pedro1@email.com', 'address' => 'Catbalogan City, Samar', 'course' => 'BS Agriculture', 'year' => 4, 'school' => 'ESSU Taft', 'gwa' => 1.30, 'annual_income' => 110000, 'status' => 'Pending'],
    ['name' => 'Sofia Martinez', 'dob' => '2003-06-18', 'gender' => 'Female', 'contact' => '09171111116', 'email' => 'sofia1@email.com', 'address' => 'Basey, Samar', 'course' => 'BS Accountancy', 'year' => 1, 'school' => 'ESSU Main', 'gwa' => 1.40, 'annual_income' => 200000, 'status' => 'Under Review'],
    ['name' => 'Miguel Torres', 'dob' => '2002-09-05', 'gender' => 'Male', 'contact' => '09171111117', 'email' => 'miguel1@email.com', 'address' => 'Paranas, Samar', 'course' => 'BS Mathematics', 'year' => 3, 'school' => 'ESSU Sta. Rita', 'gwa' => 1.20, 'annual_income' => 85000, 'status' => 'Pending'],
    ['name' => 'Isabella Fernandez', 'dob' => '2003-04-12', 'gender' => 'Female', 'contact' => '09171111118', 'email' => 'isabella1@email.com', 'address' => 'Guiuan, Eastern Samar', 'course' => 'BS Biology', 'year' => 2, 'school' => 'ESSU Borongan', 'gwa' => 1.55, 'annual_income' => 130000, 'status' => 'Rejected'],
    ['name' => 'Carlos Lopez', 'dob' => '2002-07-22', 'gender' => 'Male', 'contact' => '09171111119', 'email' => 'carlos1@email.com', 'address' => 'Llorente, Eastern Samar', 'course' => 'BS Psychology', 'year' => 4, 'school' => 'ESSU Main', 'gwa' => 1.38, 'annual_income' => 105000, 'status' => 'Pending'],
    ['name' => 'Elena Rodriguez', 'dob' => '2003-02-14', 'gender' => 'Female', 'contact' => '09171111120', 'email' => 'elena1@email.com', 'address' => 'Maydolong, Eastern Samar', 'course' => 'BS Social Work', 'year' => 1, 'school' => 'ESSU Can-avid', 'gwa' => 1.42, 'annual_income' => 145000, 'status' => 'Under Review'],
];

function generateAvatar($name) {
    $parts = explode(' ', $name);
    $first = strtoupper(substr($parts[0], 0, 1));
    $second = isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : strtoupper(substr($parts[0], 1, 1));
    return $first . $second;
}

foreach ($applicantData as $applicant) {
    $id = 'A' . substr(md5(uniqid()), 0, 8);
    $avatar = generateAvatar($applicant['name']);
    $documents = json_encode([]);
    $remarks = '';

    // Assign to variables for bind_param (PHP 8.x requires references)
    $name = $applicant['name'];
    $dob = $applicant['dob'];
    $gender = $applicant['gender'];
    $contact = $applicant['contact'];
    $email = $applicant['email'];
    $address = $applicant['address'];
    $course = $applicant['course'];
    $year = $applicant['year'];
    $school = $applicant['school'];
    $gwa = $applicant['gwa'];
    $annual_income = $applicant['annual_income'];
    $status = $applicant['status'];

    echo "Inserting: Name=$name, Course=$course, School=$school\n";

    $stmt = $conn->prepare("INSERT INTO applicants (id, name, dob, gender, contact, email, address, course, year, school, gwa, annual_income, status, documents, submitted_date, remarks, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?)");
    $stmt->bind_param("sssssssisddsssss", $id, $name, $dob, $gender, $contact, $email, $address, $course, $year, $school, $gwa, $annual_income, $status, $documents, $remarks, $avatar);
    $stmt->execute();
    $stmt->close();
}

echo "Inserted 10 applicants with course and school data\n";

// Verify the data
$result = $conn->query('SELECT id, name, course, school FROM applicants');
echo "\nVerification:\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}, Name: {$row['name']}, Course: {$row['course']}, School: {$row['school']}\n";
}
