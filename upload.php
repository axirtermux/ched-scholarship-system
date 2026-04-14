<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';

header('Content-Type: application/json');

try {
    $conn = getDBConnection();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    if (!isset($_FILES['file']) || !isset($_POST['applicant_id']) || !isset($_POST['document_type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

    $file = $_FILES['file'];
    $applicantId = $_POST['applicant_id'];
    $documentType = $_POST['document_type'];

    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type. Only images and PDFs are allowed.']);
        exit;
    }

    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['error' => 'File size exceeds 5MB limit.']);
        exit;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(500);
        echo json_encode(['error' => 'File upload error: ' . $file['error']]);
        exit;
    }

    // Determine upload directory based on file type
    $isPdf = $file['type'] === 'application/pdf';
    $uploadDir = $isPdf ? 'uploads/pdfs/' : 'uploads/images/';

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Create unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid() . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $uniqueName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move uploaded file']);
        exit;
    }

    // Store file information in database
    $stmt = $conn->prepare("INSERT INTO documents (applicant_id, document_type, file_name, file_path, file_size, file_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $applicantId, $documentType, $file['name'], $filePath, $file['size'], $file['type']);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'document_id' => $conn->insert_id,
            'file_name' => $file['name'],
            'file_path' => $filePath,
            'file_size' => $file['size'],
            'file_type' => $file['type']
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $stmt->error]);
        // Clean up uploaded file if database insert fails
        unlink($filePath);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
