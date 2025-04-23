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

// Get leave requests for the user
$leaveRequestsSql = "SELECT lr.*, lt.type_name as leave_type_name
                     FROM leave_requests lr
                     LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
                     WHERE lr.user_id = ? AND lr.user_type = ?
                     ORDER BY lr.created_at DESC
                     LIMIT ? OFFSET ?";
$leaveRequestsStmt = $conn->prepare($leaveRequestsSql);
$leaveRequestsStmt->bind_param("isii", $user_id, $user_type, $limit, $offset);
$leaveRequestsStmt->execute();
$leaveRequestsResult = $leaveRequestsStmt->get_result();

$leaveRequests = [];
while ($request = $leaveRequestsResult->fetch_assoc()) {
    $leaveRequests[] = $request;
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM leave_requests WHERE user_id = ? AND user_type = ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("is", $user_id, $user_type);
$countStmt->execute();
$countResult = $countStmt->get_result();
$count = $countResult->fetch_assoc();
$totalRequests = $count['total'];

echo json_encode([
    'status' => true,
    'message' => 'Leave requests retrieved successfully',
    'data' => [
        'leave_requests' => $leaveRequests,
        'total' => $totalRequests,
        'limit' => $limit,
        'offset' => $offset
    ]
]); 