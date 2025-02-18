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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    switch ($action) {
        case "approve_fee":
            $fee_id = $_POST['fee_id'];
            $approved_by = $_SESSION['user_id']; // Logged-in user's ID
            $approved_on = date("Y-m-d H:i:s");

            $query = "UPDATE fees SET status = 'paid', approved_by = '$approved_by', approved_on = '$approved_on' WHERE id = '$fee_id'";
            if ($conn->query($query) === TRUE) {
                echo "success";
            } else {
                echo "error";
            }
            break;
        
        // Future updates for other tables can be added here.
    }
}
?>