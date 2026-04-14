<?php
require_once 'config.php';

$conn = getDBConnection();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function to generate avatar initials
function generateAvatar($name) {
    $parts = explode(' ', $name);
    $first = strtoupper(substr($parts[0], 0, 1));
    $second = isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : strtoupper(substr($parts[0], 1, 1));
    return $first . $second;
}

// Clear existing data (optional - comment out if you want to keep existing data)
$conn->query("DELETE FROM documents");
$conn->query("DELETE FROM grantees");
$conn->query("DELETE FROM applicants");
$conn->query("DELETE FROM users");

// ==================== INSERT 5 ADMINS ====================
$adminData = [
    ['name' => 'Admin User One', 'email' => 'admin1@ched.gov.ph', 'password' => 'admin123', 'contact' => '09171234567', 'campus' => 'ESSU Main'],
    ['name' => 'Admin User Two', 'email' => 'admin2@ched.gov.ph', 'password' => 'admin123', 'contact' => '09181234567', 'campus' => 'ESSU Borongan'],
    ['name' => 'Admin User Three', 'email' => 'admin3@ched.gov.ph', 'password' => 'admin123', 'contact' => '09191234567', 'campus' => 'ESSU Salcedo'],
    ['name' => 'Admin User Four', 'email' => 'admin4@ched.gov.ph', 'password' => 'admin123', 'contact' => '09201234567', 'campus' => 'ESSU Taft'],
    ['name' => 'Admin User Five', 'email' => 'admin5@ched.gov.ph', 'password' => 'admin123', 'contact' => '09211234567', 'campus' => 'ESSU Sta. Rita'],
];

foreach ($adminData as $admin) {
    $id = 'U' . substr(md5(uniqid()), 0, 6);
    $avatar = generateAvatar($admin['name']);
    $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role, status, contact, campus, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $role = 'Administrator';
    $status = 'Active';
    $stmt->bind_param("sssssssss", $id, $admin['name'], $admin['email'], $admin['password'], $role, $status, $admin['contact'], $admin['campus'], $avatar);
    $stmt->execute();
    $stmt->close();
}

echo "Inserted 5 admin users\n";

// ==================== INSERT 10 APPLICANTS ====================
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

$applicantIds = [];
foreach ($applicantData as $index => $applicant) {
    $id = 'A' . substr(md5(uniqid()), 0, 8);
    $avatar = generateAvatar($applicant['name']);
    $documents = json_encode([]);
    $remarks = '';

    // Use direct SQL query to avoid bind_param issues with course/school
    $sql = "INSERT INTO applicants (id, name, dob, gender, contact, email, address, course, year, school, gwa, annual_income, status, documents, submitted_date, remarks, avatar) VALUES (
        '{$id}',
        '{$conn->real_escape_string($applicant['name'])}',
        '{$applicant['dob']}',
        '{$applicant['gender']}',
        '{$applicant['contact']}',
        '{$conn->real_escape_string($applicant['email'])}',
        '{$conn->real_escape_string($applicant['address'])}',
        '{$conn->real_escape_string($applicant['course'])}',
        {$applicant['year']},
        '{$conn->real_escape_string($applicant['school'])}',
        {$applicant['gwa']},
        {$applicant['annual_income']},
        '{$applicant['status']}',
        '{$documents}',
        CURDATE(),
        '{$conn->real_escape_string($remarks)}',
        '{$avatar}'
    )";
    $conn->query($sql);
    $applicantIds[] = $id;
    
    // Also create a user account for each applicant
    $userId = 'U' . substr(md5(uniqid()), 0, 6);
    $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role, status, contact, campus, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $userRole = 'Applicant';
    $userStatus = 'Active';
    $userCampus = $applicant['school'];
    $userPassword = 'password123';
    $stmt->bind_param("sssssssss", $userId, $applicant['name'], $applicant['email'], $userPassword, $userRole, $userStatus, $applicant['contact'], $userCampus, $avatar);
    $stmt->execute();
    $stmt->close();
}

echo "Inserted 10 applicants with user accounts\n";

// ==================== INSERT 10 GRANTEES ====================
$granteeData = [
    ['name' => 'Grantee One', 'course' => 'BS Education', 'year' => 2, 'school' => 'ESSU Main', 'gwa' => 1.25, 'attendance' => 95, 'stipend' => 'Active', 'sem' => '1st Semester', 'contact' => '09172222221', 'email' => 'grantee1@email.com'],
    ['name' => 'Grantee Two', 'course' => 'BS Nursing', 'year' => 3, 'school' => 'ESSU Borongan', 'gwa' => 1.30, 'attendance' => 92, 'stipend' => 'Active', 'sem' => '1st Semester', 'contact' => '09172222222', 'email' => 'grantee2@email.com'],
    ['name' => 'Grantee Three', 'course' => 'BS Engineering', 'year' => 4, 'school' => 'ESSU Main', 'gwa' => 1.35, 'attendance' => 88, 'stipend' => 'Active', 'sem' => '2nd Semester', 'contact' => '09172222223', 'email' => 'grantee3@email.com'],
    ['name' => 'Grantee Four', 'course' => 'BS IT', 'year' => 2, 'school' => 'ESSU Salcedo', 'gwa' => 1.40, 'attendance' => 90, 'stipend' => 'At Risk', 'sem' => '1st Semester', 'contact' => '09172222224', 'email' => 'grantee4@email.com'],
    ['name' => 'Grantee Five', 'course' => 'BS Agriculture', 'year' => 3, 'school' => 'ESSU Taft', 'gwa' => 1.28, 'attendance' => 94, 'stipend' => 'Active', 'sem' => '2nd Semester', 'contact' => '09172222225', 'email' => 'grantee5@email.com'],
    ['name' => 'Grantee Six', 'course' => 'BS Accountancy', 'year' => 4, 'school' => 'ESSU Main', 'gwa' => 1.32, 'attendance' => 91, 'stipend' => 'Active', 'sem' => '1st Semester', 'contact' => '09172222226', 'email' => 'grantee6@email.com'],
    ['name' => 'Grantee Seven', 'course' => 'BS Mathematics', 'year' => 2, 'school' => 'ESSU Sta. Rita', 'gwa' => 1.38, 'attendance' => 87, 'stipend' => 'Suspended', 'sem' => '2nd Semester', 'contact' => '09172222227', 'email' => 'grantee7@email.com'],
    ['name' => 'Grantee Eight', 'course' => 'BS Biology', 'year' => 3, 'school' => 'ESSU Borongan', 'gwa' => 1.45, 'attendance' => 93, 'stipend' => 'Active', 'sem' => '1st Semester', 'contact' => '09172222228', 'email' => 'grantee8@email.com'],
    ['name' => 'Grantee Nine', 'course' => 'BS Psychology', 'year' => 4, 'school' => 'ESSU Main', 'gwa' => 1.42, 'attendance' => 89, 'stipend' => 'At Risk', 'sem' => '2nd Semester', 'contact' => '09172222229', 'email' => 'grantee9@email.com'],
    ['name' => 'Grantee Ten', 'course' => 'BS Social Work', 'year' => 2, 'school' => 'ESSU Can-avid', 'gwa' => 1.36, 'attendance' => 96, 'stipend' => 'Active', 'sem' => '1st Semester', 'contact' => '09172222230', 'email' => 'grantee10@email.com'],
];

foreach ($granteeData as $index => $grantee) {
    $id = 'G' . substr(md5(uniqid()), 0, 6);
    $avatar = generateAvatar($grantee['name']);
    $applicantId = isset($applicantIds[$index]) ? $applicantIds[$index] : null;
    $prevGwa = $grantee['gwa'] - 0.05;
    $stipendAmt = 7500;
    $grades = json_encode([]);
    $alerts = json_encode([]);
    $history = json_encode([]);
    
    $stmt = $conn->prepare("INSERT INTO grantees (id, name, applicant_id, course, year, school, gwa, previous_gwa, attendance, grades, stipend_status, alerts, semester, stipend_amount, contact, email, avatar, history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssidddisssdsssss", $id, $grantee['name'], $applicantId, $grantee['course'], $grantee['year'], $grantee['school'], $grantee['gwa'], $prevGwa, $grantee['attendance'], $grades, $grantee['stipend'], $alerts, $grantee['sem'], $stipendAmt, $grantee['contact'], $grantee['email'], $avatar, $history);
    $stmt->execute();
    $stmt->close();
    
    // Also create a user account for each grantee
    $userId = 'U' . substr(md5(uniqid()), 0, 6);
    $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role, status, contact, campus, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $userRole = 'Grantee';
    $userStatus = 'Active';
    $userCampus = $grantee['school'];
    $userPassword = 'password123';
    $stmt->bind_param("sssssssss", $userId, $grantee['name'], $grantee['email'], $userPassword, $userRole, $userStatus, $grantee['contact'], $userCampus, $avatar);
    $stmt->execute();
    $stmt->close();
}

echo "Inserted 10 grantees with user accounts\n";

$conn->close();

echo "\n=== Dummy data insertion completed successfully! ===\n";
echo "Summary:\n";
echo "- 5 Admin users\n";
echo "- 10 Applicants with user accounts\n";
echo "- 10 Grantees with user accounts\n";
echo "\nDefault passwords:\n";
echo "- Admins: admin123\n";
echo "- Applicants/Grantees: password123\n";
