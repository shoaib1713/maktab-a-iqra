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

// Get work shifts from settings
$shiftsSql = "SELECT setting_value FROM attendance_settings WHERE setting_key = 'work_shifts'";
$shiftsResult = $conn->query($shiftsSql);

$workShifts = [];
if ($shiftsResult && $shiftsResult->num_rows > 0) {
    $shiftsData = $shiftsResult->fetch_assoc();
    $workShifts = json_decode($shiftsData['setting_value'], true);
}

// Default shift if none found
if (empty($workShifts)) {
    $workShifts = [
        [
            'start' => '09:00',
            'end' => '17:00',
            'min_hours' => 8
        ]
    ];
}

echo json_encode([
    'status' => true,
    'message' => 'Work shifts retrieved successfully',
    'data' => $workShifts
]); 