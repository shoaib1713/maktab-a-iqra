<?php
header('Content-Type: application/json');
// Disable error output - very important to prevent PHP errors from breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Include dependencies
require_once '../config.php';
require_once __DIR__ . '/../../config/db.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => false,
        'message' => 'Method not allowed',
        'data' => null
    ]);
    exit();
}

// Get auth token
$token = getBearerToken();
if (!$token) {
    echo json_encode([
        'status' => false,
        'message' => 'Authorization token required',
        'data' => null
    ]);
    exit();
}

// Get user information including role
$userSql = "SELECT id, role FROM users WHERE token = ? AND token_expiry > NOW()";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("s", $token);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid or expired token',
        'data' => null
    ]);
    exit();
}

// Get the user data
$userData = $userResult->fetch_assoc();
$user_id = $userData['id'];
$user_type = $userData['role']; // Map role to user_type

// Get post data
$leave_type_id = isset($_POST['leave_type_id']) ? intval($_POST['leave_type_id']) : 0;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

// Validate input
if (empty($leave_type_id) || empty($start_date) || empty($end_date)) {
    echo json_encode([
        'status' => false,
        'message' => 'Missing required fields',
        'data' => null
    ]);
    exit();
}

// Validate dates
$startDateObj = new DateTime($start_date);
$endDateObj = new DateTime($end_date);

if ($startDateObj > $endDateObj) {
    echo json_encode([
        'status' => false,
        'message' => 'Start date cannot be after end date',
        'data' => null
    ]);
    exit();
}

// Check if leave type exists
$leaveTypeSql = "SELECT id FROM leave_types WHERE id = ?";
$leaveTypeStmt = $conn->prepare($leaveTypeSql);
$leaveTypeStmt->bind_param("i", $leave_type_id);
$leaveTypeStmt->execute();
$leaveTypeResult = $leaveTypeStmt->get_result();

if ($leaveTypeResult->num_rows === 0) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid leave type',
        'data' => null
    ]);
    exit();
}

// Insert leave request
$insertSql = "INSERT INTO leave_requests 
              (user_id, user_type, leave_type_id, start_date, end_date, reason, status) 
              VALUES (?, ?, ?, ?, ?, ?, 'pending')";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("isisss", $user_id, $user_type, $leave_type_id, $start_date, $end_date, $reason);
$insertResult = $insertStmt->execute();

if ($insertResult) {
    // Get the inserted record ID
    $requestId = $conn->insert_id;
    
    // Get the inserted record with leave type name
    $getSql = "SELECT lr.*, lt.type_name as leave_type_name
               FROM leave_requests lr
               LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
               WHERE lr.id = ?";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param("i", $requestId);
    $getStmt->execute();
    $getResult = $getStmt->get_result();
    $request = $getResult->fetch_assoc();
    
    echo json_encode([
        'status' => true,
        'message' => 'Leave request submitted successfully',
        'data' => $request
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to submit leave request',
        'data' => null
    ]);
} 