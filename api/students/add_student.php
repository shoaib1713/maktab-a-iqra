<?php
// Disable error output - very important to prevent PHP errors from breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Set proper headers
header('Content-Type: application/json');
ob_clean(); // Clean any previous output buffer

// Include dependencies
try {
    require_once '../config.php';
    require_once __DIR__ . '/../../config/db.php';
} catch (Exception $e) {
    // If includes fail, return a properly formatted JSON error
    echo json_encode([
        'success' => false,
        'message' => 'Server configuration error',
        'data' => null
    ]);
    exit;
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

// Get auth token
$token = getBearerToken();
if (!$token) {
    sendError('No token provided', 401);
}

if (!validateToken($token)) {
    sendError('Invalid token', 401);
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['name']) || !isset($data['class']) || !isset($data['phone']) || !isset($data['annual_fees'])) {
    sendError('Name, class, phone, and annual fees are required');
}

// Get user information
$userSql = "SELECT id FROM users WHERE token = ? AND token_expiry > NOW()";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("s", $token);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    sendError('Invalid or expired token', 401);
}

$userData = $userResult->fetch_assoc();
$userId = $userData['id'];

// Insert student record
$insertSql = "INSERT INTO students (
                name, 
                class, 
                phone, 
                annual_fees, 
                assigned_teacher, 
                created_by, 
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())";

$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param(
    "sssiii", 
    $data['name'],
    $data['class'],
    $data['phone'],
    $data['annual_fees'],
    $data['assigned_teacher'] ?? null,
    $userId
);

if (!$insertStmt->execute()) {
    sendError('Failed to add student', 500);
}

$studentId = $conn->insert_id;

// Get the created student record
$getSql = "SELECT s.*, u.name as teacher_name 
           FROM students s 
           LEFT JOIN users u ON s.assigned_teacher = u.id 
           WHERE s.id = ?";

$getStmt = $conn->prepare($getSql);
$getStmt->bind_param("i", $studentId);
$getStmt->execute();
$result = $getStmt->get_result();
$student = $result->fetch_assoc();

$response = [
    'success' => true,
    'message' => 'Student added successfully',
    'data' => [
        'id' => $student['id'],
        'name' => $student['name'],
        'class' => $student['class'],
        'phone' => $student['phone'],
        'annual_fees' => (float)$student['annual_fees'],
        'teacher_id' => $student['assigned_teacher'],
        'teacher_name' => $student['teacher_name'],
        'created_at' => $student['created_at']
    ]
];

sendResponse($response, 201); 