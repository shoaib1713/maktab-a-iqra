<?php

// Disable error output - very important to prevent PHP errors from breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Set proper headers
header('Content-Type: application/json');
ob_start(); // Start output buffering

// Include dependencies
try {
    require_once '../config.php';
    require_once __DIR__ . '/../../config/db.php';
} catch (Exception $e) {
    // If includes fail, return a properly formatted JSON error
    ob_clean(); // Clear any output
    echo json_encode([
        'success' => false,
        'message' => 'Server configuration error',
        'data' => null
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

// Get bearer token - try both header and query param for flexibility with app
$token = getBearerToken();
if (!$token && isset($_GET['token'])) {
    $token = $_GET['token'];
}

// For app development, we can make token optional for testing
// but in production this should be required
$requireToken = true; // Set to false for development/testing

if ($requireToken) {
    if (!$token) {
        // Log the request headers for debugging
        $headers = getallheaders();
        file_put_contents('token_debug.log', date('Y-m-d H:i:s') . " - No token found. Headers: " . json_encode($headers) . "\n", FILE_APPEND);
        sendError('No token provided', 401);
    }

    if (!validateToken($token)) {
        // Log the token validation failure
        file_put_contents('token_debug.log', date('Y-m-d H:i:s') . " - Invalid token: " . $token . "\n", FILE_APPEND);
        sendError('Invalid token', 401);
    }
}

if (!isset($_GET['id'])) {
    sendError('Student ID is required');
}

$studentId = (int)$_GET['id'];

try {
    // Get student details
    $sql = "SELECT s.*, 
            s.class as class_name,
            s.class as class_id,
            u.name as teacher_name
            FROM students s  
            LEFT JOIN users u ON s.assigned_teacher = u.id
            WHERE s.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendError('Student not found', 404);
    }

    $student = $result->fetch_assoc();

    // Get student's fee history
    $feeSql = "SELECT f.* 
               FROM fees f 
               WHERE f.student_id = ? 
               ORDER BY f.created_at DESC";

    $feeStmt = $conn->prepare($feeSql);
    $feeStmt->bind_param("i", $studentId);
    $feeStmt->execute();
    $feeResult = $feeStmt->get_result();

    $feeHistory = [];
    while ($row = $feeResult->fetch_assoc()) {
        $feeHistory[] = [
            'id' => $row['id'],
            'amount' => (float)$row['amount'],
            'paymentDate' => $row['created_at'],
            'paymentMethod' => null,
            'receiptNumber' => null,
            'notes' => $row['notes'],
            'status' => $row['status']
        ];
    }

    // Calculate pending fees
    $totalFees = $student['annual_fees'] ?? 0;
    $paidFees = 0;

    foreach ($feeHistory as $fee) {
        if($fee['status'] == 'paid'){
            $paidFees += $fee['amount'];
        }
    }

    $pendingFees = $totalFees - $paidFees;
    if ($pendingFees < 0) $pendingFees = 0;

    $response = [
        'success' => true,
        'message' => 'Student details retrieved successfully',
        'data' => [
            'id' => $student['id'],
            'name' => $student['name'],
            'father_name' => $student['father_name'] ?? null,
            'class_id' => $student['class_id'] ?? null,
            'class_name' => $student['class_name'] ?? null,
            'class_time' => $student['class_time'] ?? null,
            'roll_number' => $student['roll_number'] ?? null,
            'phone' => $student['phone'] ?? null,
            'address' => $student['student_address'] ?? null,
            'is_active' => (bool)($student['is_deleted'] ? false : true),
            'annual_fees' => (float)($student['annual_fees'] ?? 0),
            'pending_fees' => (float)$pendingFees,
            'paid_fee' => (float)$paidFees ?? 0,
            'teacher_id' => $student['assigned_teacher'] ?? null,
            'teacher_name' => $student['teacher_name'] ?? null,
            'assigned_teacher' => $student['teacher_name'] ?? null,
            'photo' => $student['photo'] ?? null,
            'status' => $student['is_deleted'] ? 'inactive' : 'active',
            'remark' => $student['remarks'] ?? null,
            'created_at' => $student['created_at'] ?? null,
            'updated_at' => $student['updated_at'] ?? null,
            'deleted_at' => $student['deleted_at'] ?? null,
            'feeHistory' => $feeHistory
        ]
    ];

    sendResponse($response);
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
} 