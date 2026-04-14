<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

header('Content-Type: application/json');

// Helper function to check if user has admin role
function isAdmin() {
    $role = $_SERVER['HTTP_X_USER_ROLE'] ?? '';
    return in_array(strtolower($role), ['admin', 'administrator', 'reviewer']);
}

// Helper function to check if user is authenticated
function isAuthenticated() {
    $role = $_SERVER['HTTP_X_USER_ROLE'] ?? '';
    return !empty($role);
}

try {
    $conn = getDBConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    // Get path from different sources depending on server configuration
    $path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'] ?? '/';
    // Remove query string if present
    $path = parse_url($path, PHP_URL_PATH) ?: '/';

    // Remove base path if the API is in a subdirectory
    $scriptName = $_SERVER['SCRIPT_NAME'];
    if (strpos($path, $scriptName) === 0) {
        $path = substr($path, strlen($scriptName));
    }
    if (empty($path)) {
        $path = '/';
    }

    // Route handling
    switch (true) {
        case preg_match('#^/applicants$#', $path):
            handleApplicants($conn, $method);
            break;
        case preg_match('#^/applicants/(\w+)$#', $path, $matches):
            handleApplicant($conn, $method, $matches[1]);
            break;
        case preg_match('#^/grantees$#', $path):
            handleGrantees($conn, $method);
            break;
        case preg_match('#^/grantees/(\w+)$#', $path, $matches):
            handleGrantee($conn, $method, $matches[1]);
            break;
        case preg_match('#^/users$#', $path):
            handleUsers($conn, $method);
            break;
        case preg_match('#^/users/(\w+)$#', $path, $matches):
            handleUser($conn, $method, $matches[1]);
            break;
        case preg_match('#^/register$#', $path):
            handleRegister($conn, $method);
            break;
        case preg_match('#^/login$#', $path):
            handleLogin($conn, $method);
            break;
        case preg_match('#^/notifications$#', $path):
            handleNotifications($conn, $method);
            break;
        case preg_match('#^/notifications/(\d+)$#', $path, $matches):
            handleNotification($conn, $method, $matches[1]);
            break;
        case preg_match('#^/notifications/mark-all-read$#', $path):
            handleMarkAllRead($conn, $method);
            break;
        case preg_match('#^/documents$#', $path):
            handleDocuments($conn, $method);
            break;
        case preg_match('#^/documents/(\d+)$#', $path, $matches):
            handleDocument($conn, $method, $matches[1]);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
    }
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// ==================== LOGIN ====================
function handleLogin($conn, $method) {
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email and password are required']);
                return;
            }
            
            $stmt = $conn->prepare("SELECT id, name, email, password, role, status, contact, campus, avatar FROM users WHERE email = ?");
            $stmt->bind_param("s", $data['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                if ($row['password'] === $data['password']) {
                    if ($row['status'] !== 'Active') {
                        http_response_code(403);
                        echo json_encode(['success' => false, 'message' => 'Account is inactive']);
                        $stmt->close();
                        return;
                    }
                    
                    // Update last login
                    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $updateStmt->bind_param("s", $row['id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                    
                    echo json_encode(['success' => true, 'user' => $row]);
                } else {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Invalid password']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

// ==================== REGISTRATION ====================
function handleRegister($conn, $method) {
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['contact'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $data['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                $stmt->close();
                return;
            }
            $stmt->close();
            
            // Generate user ID
            $userId = 'U' . substr(md5(uniqid()), 0, 6);
            $avatar = strtoupper(substr($data['name'], 0, 1)) . strtoupper(substr(explode(' ', $data['name'])[1] ?? '', 0, 1));
            
            // Create user record
            $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role, status, contact, campus, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $role = 'Applicant';
            $status = 'Active';
            $campus = $data['campus'] ?? 'ESSU Main';
            $stmt->bind_param("sssssssss", $userId, $data['name'], $data['email'], $data['password'], $role, $status, $data['contact'], $campus, $avatar);
            
            if (!$stmt->execute()) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $stmt->error]);
                $stmt->close();
                return;
            }
            $stmt->close();
            
            echo json_encode(['success' => true, 'message' => 'Registration successful', 'userId' => $userId]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

// ==================== MARK ALL READ ====================
function handleMarkAllRead($conn, $method) {
    switch ($method) {
        case 'PUT':
            if (!isAdmin()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                return;
            }
            $stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE");
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

// ==================== APPLICANTS ====================
function handleApplicants($conn, $method) {
    switch ($method) {
        case 'GET':
            $result = $conn->query("SELECT * FROM applicants ORDER BY submitted_date DESC");
            $applicants = [];
            while ($row = $result->fetch_assoc()) {
                $row['documents'] = json_decode($row['documents'], true);
                $applicants[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $applicants]);
            break;
            
        case 'POST':
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = 'A' . time();
                $avatar = strtoupper(substr($data['name'], 0, 1)) . strtoupper(substr(explode(' ', $data['name'])[1] ?? '', 0, 1));
                $docs = json_encode($data['documents'] ?? []);

                // Use direct SQL query to avoid bind_param issues with course/school
                $sql = "INSERT INTO applicants (id, name, dob, gender, contact, email, address, course, year, school, gwa, annual_income, status, documents, submitted_date, remarks, avatar) VALUES (
                    '{$id}',
                    '{$conn->real_escape_string($data['name'])}',
                    '{$conn->real_escape_string($data['dob'])}',
                    '{$conn->real_escape_string($data['gender'])}',
                    '{$conn->real_escape_string($data['contact'])}',
                    '{$conn->real_escape_string($data['email'])}',
                    '{$conn->real_escape_string($data['address'])}',
                    '{$conn->real_escape_string($data['course'])}',
                    " . intval($data['year']) . ",
                    '{$conn->real_escape_string($data['school'])}',
                    " . floatval($data['gwa']) . ",
                    " . floatval($data['annual_income']) . ",
                    '{$conn->real_escape_string($data['status'])}',
                    '{$conn->real_escape_string($docs)}',
                    CURDATE(),
                    '{$conn->real_escape_string($data['remarks'] ?? '')}',
                    '{$avatar}'
                )";

                if ($conn->query($sql)) {
                    // Return the data that was inserted
                    $newApplicant = [
                        'id' => $id,
                        'name' => $data['name'],
                        'dob' => $data['dob'],
                        'gender' => $data['gender'],
                        'contact' => $data['contact'],
                        'email' => $data['email'],
                        'address' => $data['address'],
                        'course' => $data['course'],
                        'year' => intval($data['year']),
                        'school' => $data['school'],
                        'gwa' => floatval($data['gwa']),
                        'annual_income' => floatval($data['annual_income']),
                        'status' => $data['status'],
                        'documents' => $data['documents'] ?? [],
                        'submitted_date' => date('Y-m-d'),
                        'remarks' => $data['remarks'] ?? '',
                        'avatar' => $avatar
                    ];

                    echo json_encode(['success' => true, 'data' => $newApplicant]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => $conn->error]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleApplicant($conn, $method, $id) {
    switch ($method) {
        case 'GET':
            $stmt = $conn->prepare("SELECT * FROM applicants WHERE id = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $row['documents'] = json_decode($row['documents'], true);
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Applicant not found']);
            }
            $stmt->close();
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Build dynamic UPDATE query based on provided fields
            $updateFields = [];
            $params = [];
            $types = "";
            
            if (isset($data['name'])) {
                $updateFields[] = "name = ?";
                $params[] = $data['name'];
                $types .= "s";
            }
            if (isset($data['dob'])) {
                $updateFields[] = "dob = ?";
                $params[] = $data['dob'];
                $types .= "s";
            }
            if (isset($data['gender'])) {
                $updateFields[] = "gender = ?";
                $params[] = $data['gender'];
                $types .= "s";
            }
            if (isset($data['contact'])) {
                $updateFields[] = "contact = ?";
                $params[] = $data['contact'];
                $types .= "s";
            }
            if (isset($data['email'])) {
                $updateFields[] = "email = ?";
                $params[] = $data['email'];
                $types .= "s";
            }
            if (isset($data['address'])) {
                $updateFields[] = "address = ?";
                $params[] = $data['address'];
                $types .= "s";
            }
            if (isset($data['course'])) {
                $updateFields[] = "course = ?";
                $params[] = $data['course'];
                $types .= "s";
            }
            if (isset($data['year'])) {
                $updateFields[] = "year = ?";
                $params[] = $data['year'];
                $types .= "i";
            }
            if (isset($data['school'])) {
                $updateFields[] = "school = ?";
                $params[] = $data['school'];
                $types .= "s";
            }
            if (isset($data['gwa'])) {
                $updateFields[] = "gwa = ?";
                $params[] = $data['gwa'];
                $types .= "d";
            }
            if (isset($data['annual_income'])) {
                $updateFields[] = "annual_income = ?";
                $params[] = $data['annual_income'];
                $types .= "d";
            }
            if (isset($data['status'])) {
                $updateFields[] = "status = ?";
                $params[] = $data['status'];
                $types .= "s";
            }
            if (isset($data['documents'])) {
                $updateFields[] = "documents = ?";
                $params[] = json_encode($data['documents']);
                $types .= "s";
            }
            if (isset($data['remarks'])) {
                $updateFields[] = "remarks = ?";
                $params[] = $data['remarks'];
                $types .= "s";
            }
            
            if (empty($updateFields)) {
                http_response_code(400);
                echo json_encode(['error' => 'No fields to update']);
                break;
            }
            
            $params[] = $id;
            $types .= "s";
            
            $sql = "UPDATE applicants SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        case 'DELETE':
            $stmt = $conn->prepare("DELETE FROM applicants WHERE id = ?");
            $stmt->bind_param("s", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

// ==================== GRANTEES ====================
function handleGrantees($conn, $method) {
    switch ($method) {
        case 'GET':
            $result = $conn->query("SELECT * FROM grantees ORDER BY created_at DESC");
            $grantees = [];
            while ($row = $result->fetch_assoc()) {
                $row['grades'] = json_decode($row['grades'], true);
                $row['alerts'] = json_decode($row['alerts'], true);
                $row['history'] = json_decode($row['history'], true);
                $grantees[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $grantees]);
            break;
            
        case 'POST':
            $raw_input = file_get_contents('php://input');
            $data = json_decode($raw_input, true);
            
            // Log for debugging
            error_log("POST grantees raw input: " . $raw_input);
            error_log("POST grantees decoded data: " . json_encode($data));
            
            if ($data === null) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON data', 'raw' => $raw_input]);
                break;
            }
            
            try {
                $id = 'G' . substr(md5(uniqid()), 0, 6);
                $avatar = strtoupper(substr($data['name'], 0, 1)) . strtoupper(substr(explode(' ', $data['name'])[1] ?? '', 0, 1));
                
                // Map camelCase field names from frontend to snake_case for database
                $applicant_id = $data['applicant_id'] ?? $data['appId'] ?? null;
                $prev_gwa = $data['prev_gwa'] ?? $data['prevGwa'] ?? $data['gwa'] ?? 0;
                $stipend_amt = $data['stipend_amt'] ?? $data['stipendAmt'] ?? 7500;
                $stipend_status = $data['stipend'] ?? 'Current';
                
                // Check if applicant_id is provided and exists in applicants table
                if (isset($applicant_id) && !empty($applicant_id)) {
                    $checkStmt = $conn->prepare("SELECT id FROM applicants WHERE id = ?");
                    $checkStmt->bind_param("s", $applicant_id);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();
                    if ($checkResult->num_rows === 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid applicant_id: applicant not found']);
                        $checkStmt->close();
                        break;
                    }
                    $checkStmt->close();
                }
                
                // Update the user role from Applicant to Grantee using the email from the grantee data
                $email = $data['email'] ?? null;
                if ($email) {
                    $updateUser = $conn->prepare("UPDATE users SET role = 'Grantee' WHERE email = ? AND role = 'Applicant'");
                    $updateUser->bind_param("s", $email);
                    $updateUser->execute();
                    $updateUser->close();
                }
                
                $stmt = $conn->prepare("INSERT INTO grantees (id, name, applicant_id, course, year, school, gwa, previous_gwa, attendance, grades, stipend_status, alerts, semester, stipend_amount, contact, email, avatar, history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $grades = json_encode($data['grades'] ?? []);
                $alerts = json_encode($data['alerts'] ?? []);
                $history = json_encode($data['history'] ?? []);
                $stmt->bind_param("ssssidddisssdsssss", $id, $data['name'], $applicant_id, $data['course'], $data['year'], $data['school'], $data['gwa'], $prev_gwa, $data['attendance'], $grades, $stipend_status, $alerts, $data['sem'], $stipend_amt, $data['contact'], $data['email'], $avatar, $history);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'id' => $id]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => $stmt->error, 'sql_error' => $conn->error]);
                }
                $stmt->close();
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleGrantee($conn, $method, $id) {
    switch ($method) {
        case 'GET':
            $stmt = $conn->prepare("SELECT * FROM grantees WHERE id = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $row['grades'] = json_decode($row['grades'], true);
                $row['alerts'] = json_decode($row['alerts'], true);
                $row['history'] = json_decode($row['history'], true);
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Grantee not found']);
            }
            $stmt->close();
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if applicant_id is provided and exists in applicants table
            if (isset($data['applicant_id']) && !empty($data['applicant_id'])) {
                $checkStmt = $conn->prepare("SELECT id FROM applicants WHERE id = ?");
                $checkStmt->bind_param("s", $data['applicant_id']);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                if ($checkResult->num_rows === 0) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid applicant_id: applicant not found']);
                    $checkStmt->close();
                    break;
                }
                $checkStmt->close();
            }
            
            $grades = json_encode($data['grades'] ?? []);
            $alerts = json_encode($data['alerts'] ?? []);
            $history = json_encode($data['history'] ?? []);
            
            $stmt = $conn->prepare("UPDATE grantees SET name = ?, applicant_id = ?, course = ?, year = ?, school = ?, gwa = ?, previous_gwa = ?, attendance = ?, grades = ?, stipend_status = ?, alerts = ?, semester = ?, stipend_amount = ?, contact = ?, email = ?, history = ? WHERE id = ?");
            $stmt->bind_param("sssidddisssdsssss", $data['name'], $data['applicant_id'], $data['course'], $data['year'], $data['school'], $data['gwa'], $data['prev_gwa'], $data['attendance'], $grades, $data['stipend'], $alerts, $data['sem'], $data['stipend_amt'], $data['contact'], $data['email'], $history, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        case 'DELETE':
            $stmt = $conn->prepare("DELETE FROM grantees WHERE id = ?");
            $stmt->bind_param("s", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

// ==================== USERS ====================
function handleUsers($conn, $method) {
    switch ($method) {
        case 'GET':
            $result = $conn->query("SELECT id, name, email, role, status, contact, campus, avatar, last_login FROM users");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $users]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = 'U' . substr(md5(uniqid()), 0, 6);
            $avatar = strtoupper(substr($data['name'], 0, 1)) . strtoupper(substr(explode(' ', $data['name'])[1] ?? '', 0, 1));
            
            $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role, status, contact, campus, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $id, $data['name'], $data['email'], $data['password'], $data['role'], $data['status'], $data['contact'], $data['campus'], $avatar);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'id' => $id]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleUser($conn, $method, $id) {
    switch ($method) {
        case 'GET':
            $stmt = $conn->prepare("SELECT id, name, email, role, status, contact, campus, avatar, last_login FROM users WHERE id = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
            $stmt->close();
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ?, contact = ?, campus = ? WHERE id = ?");
            $stmt->bind_param("sssssss", $data['name'], $data['email'], $data['role'], $data['status'], $data['contact'], $data['campus'], $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        case 'DELETE':
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("s", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

// ==================== DOCUMENTS ====================
function handleDocuments($conn, $method) {
    switch ($method) {
        case 'GET':
            $applicant_id = isset($_GET['applicant_id']) ? $_GET['applicant_id'] : null;
            if ($applicant_id) {
                $stmt = $conn->prepare("SELECT id, applicant_id, document_type, file_name, file_path, file_size, file_type, uploaded_at FROM documents WHERE applicant_id = ?");
                $stmt->bind_param("s", $applicant_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $documents = [];
                while ($row = $result->fetch_assoc()) {
                    $documents[] = $row;
                }
                $stmt->close();
                echo json_encode(['success' => true, 'data' => $documents]);
            } else {
                $result = $conn->query("SELECT id, applicant_id, document_type, file_name, file_path, file_size, file_type, uploaded_at FROM documents");
                $documents = [];
                while ($row = $result->fetch_assoc()) {
                    $documents[] = $row;
                }
                echo json_encode(['success' => true, 'data' => $documents]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleDocument($conn, $method, $id) {
    switch ($method) {
        case 'DELETE':
            // First get the file path to delete the physical file
            $stmt = $conn->prepare("SELECT file_path FROM documents WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $filePath = $row['file_path'];
                // Delete the physical file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $stmt->close();

            // Delete the database record
            $stmt = $conn->prepare("DELETE FROM documents WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

// ==================== NOTIFICATIONS ====================
function handleNotifications($conn, $method) {
    switch ($method) {
        case 'GET':
            // Allow unauthenticated access to notifications
            $result = $conn->query("SELECT * FROM notifications ORDER BY time DESC");
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $notifications]);
            break;
            
        case 'POST':
            if (!isAdmin()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                return;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $conn->prepare("INSERT INTO notifications (message, time, is_read, type, link) VALUES (?, NOW(), ?, ?, ?)");
            $stmt->bind_param("siss", $data['message'], $data['read'], $data['type'], $data['link']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'id' => $conn->insert_id]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleNotification($conn, $method, $id) {
    switch ($method) {
        case 'PUT':
            if (!isAuthenticated()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Please login']);
                return;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['read'])) {
                $stmt = $conn->prepare("UPDATE notifications SET is_read = ? WHERE id = ?");
                $stmt->bind_param("ii", $data['read'], $id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid request']);
                return;
            }
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        case 'DELETE':
            if (!isAdmin()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                return;
            }
            $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}
?>
