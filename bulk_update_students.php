<?php
session_start();
require 'config/db.php';
require 'student_history.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['student_ids']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$student_ids = $_POST['student_ids'];
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];
$year = date('Y');
$month = date('m');

// Validate action
if (!in_array($action, ['activate', 'deactivate'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Prepare the query
    $status = $action === 'activate' ? 0 : 1;
    $placeholders = str_repeat('?,', count($student_ids) - 1) . '?';
    $types = str_repeat('i', count($student_ids));

    $query = "UPDATE students SET is_deleted = ? WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($query);

    // Combine parameters
    $params = array_merge([$status], $student_ids);
    $types = 'i' . $types;

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Update status history for each student
        foreach ($student_ids as $student_id) {
            // Get student's current details
            $studentQuery = "SELECT assigned_teacher, annual_fees FROM students WHERE id = ?";
            $studentStmt = $conn->prepare($studentQuery);
            $studentStmt->bind_param("i", $student_id);
            $studentStmt->execute();
            $studentResult = $studentStmt->get_result();
            $student = $studentResult->fetch_assoc();

            // Update history with current status
            updateStudentStatusHistory(
                $conn,
                $user_id,
                $student_id,
                $year,
                $student['assigned_teacher'],
                $student['annual_fees'],
                $action === 'activate' ? 'active' : 'inactive'
            );
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Students updated successfully']);
    } else {
        throw new Exception('Failed to update students');
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 