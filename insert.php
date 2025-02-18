<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require 'config/db.php';
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case "add_cheque":
            $created_by = $_SESSION['user_id'];
            $created_on = date("Y-m-d H:i:s");

            foreach ($_POST['cheque_given_date'] as $index => $date) {
                $cheque_number = $_POST['cheque_number'][$index];
                $amount = $_POST['cheque_amount'][$index];
                $cheque_handover_teacher = $_POST['cheque_handover_teacher'][$index];
                $cheque_year = $_POST['cheque_year'][$index];
                $cheque_month = $_POST['cheque_month'][$index];

                // Handle File Upload
                $photo_name = $_FILES['cheque_photo']['name'][$index];
                $photo_tmp = $_FILES['cheque_photo']['tmp_name'][$index];
                $photo_path = "assets/images/" . time() . "_" . basename($photo_name);
                move_uploaded_file($photo_tmp, $photo_path);

                // Insert into database
                $stmt = $conn->prepare("INSERT INTO cheque_details (cheque_given_date, cheque_number, cheque_amount, cheque_photo, cheque_handover_teacher,cheque_year,cheque_month, created_by, created_on) VALUES (?,?,?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssis", $date, $cheque_number, $amount, $photo_path, $cheque_handover_teacher,$cheque_year,$cheque_month, $created_by, $created_on);
                $stmt->execute();
            }

            echo "success";
            break;

        case "add_maintenance":
            $created_by = $_SESSION['user_id'];
            $created_on = date("Y-m-d H:i:s");

            foreach ($_POST['category'] as $index => $category) {
                $amount = $_POST['amount'][$index];
                $comment = isset($_POST['comment'][$index]) ? $_POST['comment'][$index] : '';

                $stmt = $conn->prepare("INSERT INTO maintenance (category, amount, comment, created_by, created_on) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sdsis", $category, $amount, $comment, $created_by, $created_on);
                $stmt->execute();
            }

            echo "success";
            break;

        default:
            echo "Invalid action!";
            break;
    }
} else {
    echo "No data received.";
}
?>
