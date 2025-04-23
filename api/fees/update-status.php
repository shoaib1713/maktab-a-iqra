<?php
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
        'message' => 'Server configuration error'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

$token = getBearerToken();
if (!$token) {
    sendError('No token provided', 401);
}

if (!validateToken($token)) {
    sendError('Invalid token', 401);
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['fee_id']) || !isset($data['status']) || !isset($data['user_id'])) {
    sendError('Fee ID, status, and user ID are required');
}

$fee_id = (int)$data['fee_id'];
$status = $data['status']; // 'paid' for approved, 'rejected' for rejected
$user_id = (int)$data['user_id'];
$reason = isset($data['reason']) ? $data['reason'] : '';
$current_time = date("Y-m-d H:i:s");

// Validate fee exists and is in pending status
$checkSql = "SELECT id, status FROM fees WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $fee_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    sendError('Fee record not found', 404);
}

$feeRecord = $checkResult->fetch_assoc();
if ($feeRecord['status'] !== 'pending') {
    sendError('Fee is not in pending status', 400);
}

// Update fee status
if ($status === 'paid') {
    // Approve fee
    $updateSql = "UPDATE fees SET 
                   status = 'paid', 
                   approved_by = ?, 
                   approved_on = ? 
                   WHERE id = ?";
    
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("isi", $user_id, $current_time, $fee_id);
} else if ($status === 'rejected') {
    // Reject fee
    $updateSql = "UPDATE fees SET 
                   status = 'rejected', 
                   rejected_by = ?, 
                   rejected_on = ?,
                   reason = ?
                   WHERE id = ?";
    
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("issi", $user_id, $current_time, $reason, $fee_id);
} else {
    sendError('Invalid status value. Must be "paid" or "rejected"', 400);
}

if (!$updateStmt->execute()) {
    sendError('Failed to update fee status: ' . $conn->error, 500);
}

// Get the updated fee record
$getSql = "SELECT f.*, s.name as student_name, u1.name as created_by_name, 
           u2.name as approved_by_name, u3.name as rejected_by_name
           FROM fees f 
           LEFT JOIN students s ON s.id = f.student_id
           LEFT JOIN users u1 ON u1.id = f.created_by
           LEFT JOIN users u2 ON u2.id = f.approved_by
           LEFT JOIN users u3 ON u3.id = f.rejected_by
           WHERE f.id = ?";

$getStmt = $conn->prepare($getSql);
$getStmt->bind_param("i", $fee_id);
$getStmt->execute();
$result = $getStmt->get_result();
$fee = $result->fetch_assoc();

$response = [
    'success' => true,
    'message' => $status === 'paid' ? 'Fee approved successfully' : 'Fee rejected successfully',
    'data' => [
        'id' => (int)$fee['id'],
        'student_id' => (int)$fee['student_id'],
        'student_name' => $fee['student_name'],
        'amount' => (float)$fee['amount'],
        'month' => (int)$fee['month'],
        'year' => $fee['Year'],
        'status' => $fee['status'],
        'created_at' => $fee['created_at'],
        'created_by' => (int)$fee['created_by'],
        'created_by_name' => $fee['created_by_name'],
        'approved_by' => $fee['approved_by'] ? (int)$fee['approved_by'] : null,
        'approved_by_name' => $fee['approved_by_name'],
        'approved_on' => $fee['approved_on'],
        'rejected_by' => $fee['rejected_by'] ? (int)$fee['rejected_by'] : null,
        'rejected_by_name' => $fee['rejected_by_name'],
        'rejected_on' => $fee['rejected_on'],
        'reason' => $fee['reason']
    ]
];

sendResponse($response);