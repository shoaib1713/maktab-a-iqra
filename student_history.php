<?php 
function updateStudentStatusHistory($conn, $user_id, $student_id, $year, $assigned_teacher=null, $salana_fees=0, $status = 'active') {

    $student = array();
    if (isset($student_id)) {
        $query = "SELECT * FROM students WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
    }

    if(empty($student)){
        echo "Student Not found";
        return;
    }
    if(is_null($assigned_teacher)){
        $assigned_teacher = $student['assigned_teacher'];
    }
    if(is_null($salana_fees)){
        $salana_fees = $student['annual_fees'];
    }
    // Step 1: Mark previous record as inactive (if any)
    $month = date('m');
    $updateQuery = "UPDATE student_status_history 
                    SET current_active_record = 1 , updated_by = $user_id , updated_at = now()
                    WHERE student_id = ? AND current_active_record = 0";
    
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $student_id);
    $updateStmt->execute();
    $updateStmt->close();

    // Step 2: Insert new entry into student_status_history
    $insertQuery = "INSERT INTO student_status_history (student_id, year, month, assigned_teacher, salana_fees, status, current_active_record,created_by)
                    VALUES (?, ?, ?, ?, ?, ?, 0, ?)";
    
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iiiidsi", $student_id, $year,$month ,$assigned_teacher, $salana_fees, $status, $user_id);
    $insertStmt->execute();
    $insertStmt->close();
}

?>