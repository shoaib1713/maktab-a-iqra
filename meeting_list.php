<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

function getUserName($userId, $conn) {
    if (!$userId) return "N/A"; // If no user assigned, return "N/A"
    
    $query = $conn->query("SELECT name FROM users WHERE id = '$userId' LIMIT 1");
    $user = $query->fetch_assoc();
    
    return $user ? $user['name'] : "Unknown";
}

$searchQuery = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $year = $_POST['year'];
    $month = $_POST['month'];
    $searchQuery = "WHERE YEAR(meeting_date) = '$year' AND MONTH(meeting_date) = '$month'";
}

$query = "SELECT id, meeting_date, student_responsibility, namaz_responsibility, visit_fajar, visit_asar, visit_magrib, maktab_lock,cleanliness_ethics,food_responsibility 
          FROM meeting_details 
          $searchQuery
          ORDER BY created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h2 class="mb-4">Meeting Details</h2>

            <!-- Search Form -->
            <form method="POST" class="row mb-3">
                <div class="col-md-4">
                    <label>Year:</label>
                    <select name="year" class="form-control">
                        <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo ( isset($_POST['year']) && $_POST['year'] == $y) ? 'selected' : '' ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Month:</label>
                    <select name="month" class="form-control">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo (isset($_POST['month']) && $_POST['month']==$m)?'selected':''?>><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
            <div class="col-md-4">
                <a href="add_meeting_details.php" class="btn btn-success">Add Meeting</a>
            </div>
            <!-- Meeting List -->
            <table id="meetingTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Student Responsibility</th>
                                <th>Namaz Responsibility</th>
                                <th>Daily Visits</th>
                                <th>Fees Collection</th>
                                <th>Maktab Lock</th>
                                <th>Safai aur akhlak</th>
                                <th>Food Responsibility</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('F-Y', strtotime($row['meeting_date'])); ?></td>
                                    <td><?= getUserName($row['student_responsibility'], $conn) ?></td>
                                    <td><?= getUserName($row['namaz_responsibility'], $conn) ?></td>
                                    <td>
                                        Fajar: <?= getUserName($row['visit_fajar'], $conn) ?><br>
                                        Asar: <?= getUserName($row['visit_asar'], $conn) ?><br>
                                        Magrib: <?= getUserName($row['visit_magrib'], $conn) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $feesQuery = $conn->query("SELECT admin_id, amount FROM meeting_fees_collection WHERE meeting_id = " . $row['id']);
                                        while ($fee = $feesQuery->fetch_assoc()) {
                                            echo getUserName($fee['admin_id'], $conn) . " - ₹" . $fee['amount'] . "<br>";
                                        }
                                        ?>
                                    </td>
                                    <td><?= getUserName($row['maktab_lock'], $conn) ?></td>
                                    <td><?= getUserName($row['cleanliness_ethics'], $conn) ?></td>
                                    <td><?= getUserName($row['food_responsibility'], $conn) ?></td>
                                    <td><?= date("d-m-Y", strtotime($row['meeting_date'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
        </div>
    </div>
</body>
</html>
<script>
</script>