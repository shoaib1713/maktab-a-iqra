<?php
// Disable error output - very important to prevent PHP errors from breaking JSON
// error_reporting(0);
// ini_set('display_errors', 0);

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

// Set proper headers
header('Content-Type: application/json');
ob_clean(); // Clean any previous output buffer

// Include dependencies
try {
    require_once './config.php';
    require_once __DIR__ . '/../config/db.php';
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
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

// Get user information including role
$userSql = "SELECT id, role FROM users WHERE token = ? AND token_expiry > NOW()";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("s", $token);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    sendError('Invalid or expired token', 401);
}

// Get the user data
$userData = $userResult->fetch_assoc();
$userId = $userData['id'];
$userRole = $userData['role'];

// Prepare where clause based on user role
$whereClause = '';
if ($userRole == 'teacher') {
    $whereClause = ' AND s.assigned_teacher = ' . $userId;
}

// Get student bifurcation data with class times
$sql = "SELECT 
            s.class as class_number,
            s.class_time,
            COUNT(s.id) as student_count,
            GROUP_CONCAT(
                JSON_OBJECT(
                    'id', s.id,
                    'name', s.name,
                    'class_name', s.class,
                    'phone', s.phone,
                    'annual_fees', s.annual_fees,
                    'teacher_id', s.assigned_teacher,
                    'teacher_name', t.name,
                    'status', CASE WHEN s.is_deleted = 0 THEN 'Active' ELSE 'Inactive' END,
                    'photo', s.photo
                )
            ) as students
        FROM students s
        LEFT JOIN users t ON s.assigned_teacher = t.id
        WHERE s.is_deleted = 0" . $whereClause . "
        GROUP BY s.class, s.class_time
        ORDER BY s.class, s.class_time";

$result = $conn->query($sql);

if (!$result) {
    sendError('Failed to get student bifurcation data');
}

$classInfo = [];
$currentClass = null;
$classTimes = [];

while ($row = $result->fetch_assoc()) {
    $classNumber = (int)$row['class_number'];
    $time = $row['class_time'];
    $studentCount = (int)$row['student_count'];
    
    // Parse students JSON
    $students = [];
    if ($row['students']) {
        $studentArray = json_decode('[' . $row['students'] . ']', true);
        foreach ($studentArray as $student) {
            $students[] = [
                'id' => (int)$student['id'],
                'name' => $student['name'],
                'class_name' => $student['class_name'],
                'phone' => $student['phone'],
                'annual_fees' => (float)$student['annual_fees'],
                'teacher_id' => (int)$student['teacher_id'],
                'teacher_name' => $student['teacher_name'],
                'status' => $student['status'],
                'photo' => $student['photo']
            ];
        }
    }
    
    if ($currentClass !== $classNumber) {
        if ($currentClass !== null) {
            $classInfo[] = [
                'classNumber' => $currentClass,
                'classTimes' => $classTimes
            ];
        }
        $currentClass = $classNumber;
        $classTimes = [];
    }
    
    $classTimes[] = [
        'time' => $time,
        'studentCount' => $studentCount,
        'students' => $students
    ];
}

// Add the last class
if ($currentClass !== null) {
    $classInfo[] = [
        'classNumber' => $currentClass,
        'classTimes' => $classTimes
    ];
}

// Send success response
sendResponse([
    'success' => true,
    'message' => 'Student bifurcation data retrieved successfully',
    'data' => $classInfo
]);
?> 