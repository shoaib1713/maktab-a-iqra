<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require 'config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$meeting_id = intval($_GET['id']);

// Fetch meeting details
$query = "SELECT * FROM meeting_details WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $meeting_id);
$stmt->execute();
$result = $stmt->get_result();
$meeting = $result->fetch_assoc();

if (!$meeting) {
    die("Meeting not found.");
}

// Function to get user name
function getUserName($user_id, $conn) {
    if (!$user_id) return "N/A";
    $query = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? $user['name'] : "N/A";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meeting Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Ensures sidebar stays on the left */
        .sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            background: #fff;
            padding-top: 20px;
            border-right: 1px solid #ddd;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="sidebar">
        <?php include 'includes/sidebar.php'; ?>
    </div>

    <div class="content">
        <div class="container mt-4">
            <h2 class="mb-4">Meeting Details</h2>

            <div class="card shadow p-4">
                <p><strong>Student Responsibility:</strong> <?= getUserName($meeting['student_responsibility'], $conn); ?></p>
                <p><strong>Namaz Responsibility:</strong> <?= getUserName($meeting['namaz_responsibility'], $conn); ?></p>

                <h4>Daily Visits</h4>
                <ul>
                    <li><strong>After Fajar:</strong> <?= getUserName($meeting['fajar_visit'], $conn); ?></li>
                    <li><strong>After Asar:</strong> <?= getUserName($meeting['asar_visit'], $conn); ?></li>
                    <li><strong>After Magrib:</strong> <?= getUserName($meeting['magrib_visit'], $conn); ?></li>
                </ul>

                <h4>Fees Collection of Committee</h4>
                <p><?= $meeting['fees_collection']; ?></p>

                <p><strong>Maktab Lock Responsibility:</strong> <?= getUserName($meeting['maktab_lock'], $conn); ?></p>
                <p><strong>Safai & Akhlak Responsibility:</strong> <?= getUserName($meeting['safai_akhlak'], $conn); ?></p>
                <p><strong>Khana Responsibility:</strong> <?= getUserName($meeting['khana'], $conn); ?></p>

                <a href="meeting_list.php" class="btn btn-primary mt-3">Back to Meetings</a>
            </div>
        </div>
    </div>
</body>
</html>
