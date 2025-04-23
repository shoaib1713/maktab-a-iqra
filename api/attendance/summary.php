<?php
header('Content-Type: application/json');
// Disable error output - very important to prevent PHP errors from breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Include dependencies
require_once '../config.php';
require_once __DIR__ . '/../../config/db.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

// Get query parameters
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get attendance summary for the month
$summarySql = "SELECT 
                COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count,
                COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_count,
                COUNT(CASE WHEN status = 'leave' THEN 1 END) as leave_count,
                COUNT(CASE WHEN status = 'late' THEN 1 END) as late_count,
                COUNT(CASE WHEN status = 'early_exit' THEN 1 END) as early_exit_count,
                SUM(work_hours) as total_hours
               FROM attendance_summary
               WHERE user_id = ? AND user_type = ? AND month = ? AND year = ?";
$summaryStmt = $conn->prepare($summarySql);
$summaryStmt->bind_param("isii", $user_id, $user_type, $month, $year);
$summaryStmt->execute();
$summaryResult = $summaryStmt->get_result();
$summary = $summaryResult->fetch_assoc();

// Initialize default values if no records found
if ($summaryResult->num_rows === 0) {
    $summary = [
        'present_count' => 0,
        'absent_count' => 0,
        'leave_count' => 0,
        'late_count' => 0,
        'early_exit_count' => 0,
        'total_hours' => 0,
        'month' => $month,
        'year' => $year
    ];
} else {
    // Add month and year to the response
    $summary['month'] = $month;
    $summary['year'] = $year;
}

echo json_encode([
    'status' => true,
    'message' => 'Attendance summary retrieved successfully',
    'data' => $summary
]); 