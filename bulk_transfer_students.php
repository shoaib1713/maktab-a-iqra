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

if (!isset($_POST['student_ids']) || !isset($_POST['new_teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$student_ids = $_POST['student_ids'];
$new_teacher_id = intval($_POST['new_teacher_id']);
$user_id = $_SESSION['user_id'];
$year = date('Y');
$month = date('m');

// Verify the new teacher exists
$teacherQuery = "SELECT id FROM users WHERE id = ? AND role = 'teacher'";
$teacherStmt = $conn->prepare($teacherQuery);
$teacherStmt->bind_param("i", $new_teacher_id);
$teacherStmt->execute();
$teacherResult = $teacherStmt->get_result();

if ($teacherResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid teacher selected']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Prepare the query
    $placeholders = str_repeat('?,', count($student_ids) - 1) . '?';
    $types = str_repeat('i', count($student_ids));

    $query = "UPDATE students SET assigned_teacher = ? WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($query);

    // Combine parameters
    $params = array_merge([$new_teacher_id], $student_ids);
    $types = 'i' . $types;

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Update status history for each student
        foreach ($student_ids as $student_id) {
            // Get student's current details
            $studentQuery = "SELECT annual_fees FROM students WHERE id = ?";
            $studentStmt = $conn->prepare($studentQuery);
            $studentStmt->bind_param("i", $student_id);
            $studentStmt->execute();
            $studentResult = $studentStmt->get_result();
            $student = $studentResult->fetch_assoc();

            // Update history with transfer status
            updateStudentStatusHistory(
                $conn,
                $user_id,
                $student_id,
                $year,
                $new_teacher_id,
                $student['annual_fees'],
                'transferred'
            );
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Students transferred successfully']);
    } else {
        throw new Exception('Failed to transfer students');
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 