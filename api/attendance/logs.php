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
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Get all logs for the user
$logsSql = "SELECT al.*, ol_in.location_name as punch_in_location, ol_out.location_name as punch_out_location
           FROM attendance_logs al
           LEFT JOIN office_locations ol_in ON al.punch_in_location_id = ol_in.id
           LEFT JOIN office_locations ol_out ON al.punch_out_location_id = ol_out.id
           WHERE al.user_id = ? AND al.user_type = ?
           ORDER BY al.punch_in_time DESC
           LIMIT ? OFFSET ?";
$logsStmt = $conn->prepare($logsSql);
$logsStmt->bind_param("isii", $user_id, $user_type, $limit, $offset);
$logsStmt->execute();
$logsResult = $logsStmt->get_result();

$logs = [];
while ($log = $logsResult->fetch_assoc()) {
    $logs[] = $log;
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM attendance_logs WHERE user_id = ? AND user_type = ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("is", $user_id, $user_type);
$countStmt->execute();
$countResult = $countStmt->get_result();
$count = $countResult->fetch_assoc();
$totalLogs = $count['total'];

echo json_encode([
    'status' => true,
    'message' => 'Attendance logs retrieved successfully',
    'data' => [
        'logs' => $logs,
        'total' => $totalLogs,
        'limit' => $limit,
        'offset' => $offset
    ]
]); 