<?php
session_start();
require 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['student_ids']) || !isset($_POST['amount']) || !isset($_POST['month']) || !isset($_POST['year'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$student_ids = $_POST['student_ids'];
$amount = floatval($_POST['amount']);
$month = intval($_POST['month']);
$year = intval($_POST['year']);
$user_id = $_SESSION['user_id'];
$status = 'pending';

// Start transaction
$conn->begin_transaction();

try {
    // Prepare the insert query
    $query = "INSERT INTO fees (student_id, amount, month, Year, created_by, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];

    foreach ($student_ids as $student_id) {
        // Check if fee already exists for this month and year
        $checkQuery = "SELECT id FROM fees WHERE student_id = ? AND month = ? AND Year = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("iii", $student_id, $month, $year);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $errors[] = "Fee already collected for student ID: $student_id for $month/$year";
            $errorCount++;
            continue;
        }

        // Get student's annual fees
        $studentQuery = "SELECT annual_fees FROM students WHERE id = ?";
        $studentStmt = $conn->prepare($studentQuery);
        $studentStmt->bind_param("i", $student_id);
        $studentStmt->execute();
        $studentResult = $studentStmt->get_result();
        $student = $studentResult->fetch_assoc();

        if (!$student) {
            $errors[] = "Student not found: $student_id";
            $errorCount++;
            continue;
        }

        // Check if amount exceeds annual fees
        if ($amount > $student['annual_fees']) {
            $errors[] = "Amount exceeds annual fees for student ID: $student_id";
            $errorCount++;
            continue;
        }

        // Insert fee record
        $stmt->bind_param("idiisi", $student_id, $amount, $month, $year, $user_id, $status);
        
        if ($stmt->execute()) {
            $successCount++;
        } else {
            $errors[] = "Failed to collect fee for student ID: $student_id";
            $errorCount++;
        }
    }

    if ($successCount > 0) {
        $conn->commit();
        $message = "Successfully collected fees from $successCount students.";
        if ($errorCount > 0) {
            $message .= " Failed for $errorCount students.";
        }
        echo json_encode([
            'success' => true,
            'message' => $message,
            'errors' => $errors
        ]);
    } else {
        throw new Exception("Failed to collect fees for any students. " . implode(", ", $errors));
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 