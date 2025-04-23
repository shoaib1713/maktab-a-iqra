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
        'message' => 'Server configuration error',
        'data' => []
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

$token = getBearerToken();
if (!$token) {
    sendError('No token provided', 401);
}

if (!validateToken($token)) {
    sendError('Invalid token', 401);
}

// Get query parameters for filtering
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build search query conditions
$conditions = ["f.status = 'pending'"]; // Default condition for pending approvals
if (!empty($year)) $conditions[] = "f.Year = '$year'";
if (!empty($month)) $conditions[] = "f.month = $month";
if (!empty($search)) {
    $conditions[] = "(f.id LIKE '%$search%' OR s.name LIKE '%$search%' OR s.id LIKE '%$search%')";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

// Fetch pending fee approvals
$query = "SELECT 
            f.id, 
            f.student_id, 
            f.amount, 
            f.month, 
            f.Year as year, 
            f.created_at, 
            f.created_by,
            s.name as student_name, 
            s.class as class_name, 
            s.phone, 
            u.name as created_by_name
          FROM fees f 
          LEFT JOIN students s ON s.id = f.student_id 
          LEFT JOIN users u ON u.id = f.created_by
          $whereClause 
          ORDER BY f.created_at DESC";

try {
    $result = $conn->query($query);
    
    $pendingFees = [];
    while ($row = $result->fetch_assoc()) {
        $pendingFees[] = [
            'id' => (int)$row['id'],
            'student_id' => (int)$row['student_id'],
            'student_name' => $row['student_name'],
            'class_name' => $row['class_name'],
            'phone' => $row['phone'],
            'amount' => (float)$row['amount'],
            'month' => (int)$row['month'],
            'year' => $row['year'],
            'created_at' => $row['created_at'],
            'created_by' => (int)$row['created_by'],
            'created_by_name' => $row['created_by_name']
        ];
    }
    
    // Get summary statistics
    $statsSql = "SELECT 
        COUNT(*) as total_pending,
        SUM(amount) as total_amount,
        COUNT(DISTINCT student_id) as unique_students
    FROM fees f
    WHERE f.status = 'pending'";
    
    $statsResult = $conn->query($statsSql);
    $stats = $statsResult->fetch_assoc();
    
    $response = [
        'success' => true,
        'message' => 'Pending fees retrieved successfully',
        'data' => $pendingFees,
        'stats' => [
            'total_pending' => (int)$stats['total_pending'],
            'total_amount' => (float)$stats['total_amount'],
            'unique_students' => (int)$stats['unique_students']
        ]
    ];
    
    sendResponse($response);
} catch (Exception $e) {
    sendError('Failed to retrieve pending fees: ' . $e->getMessage());
}
