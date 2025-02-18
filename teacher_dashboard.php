<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$year = date("Y");

$startYear = $year - 1;
$endYear = $year;
//echo $_POST['academic_year']; exit;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['academic_year']) && $_POST['academic_year'] !== '') {
    $selectedYear = $_POST['academic_year'];
    list($startYear, $endYear) = explode("-", $selectedYear);
}
$startMonth = ACEDEMIC_START_MONTH;
$endMonth = ACEDEMIC_END_MONTH;

// Get total fees collected for the teacher's students
$sql_collected = "SELECT SUM(f.amount) AS total_collected FROM fees f 
JOIN students s ON f.student_id = s.id 
WHERE s.assigned_teacher = ? AND f.status = 'paid' AND (
    (year = $startYear AND month >= $startMonth)
    OR 
    (year = $endYear AND month <= $endMonth)
) AND is_deleted = 0 ";
//echo $sql_collected; exit;
$stmt = $conn->prepare($sql_collected);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->bind_result($total_collected);
$stmt->fetch();
$stmt->close();

// // Get pending fees for the teacher's students
// $sql_pending = "SELECT SUM(f.amount) AS total_pending FROM fees f 
// JOIN students s ON f.student_id = s.id 
// WHERE s.assigned_teacher = ? AND f.status = 'pending' AND f.month = ?";
// $stmt = $conn->prepare($sql_pending);
// $stmt->bind_param("ii", $teacher_id, $year);
// $stmt->execute();
// $stmt->bind_result($total_pending);
// $stmt->fetch();
// $stmt->close();


$countQuery = "SELECT COUNT(*) as total FROM students WHERE assigned_teacher = ?";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("i", $teacher_id);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalStudents = $countResult->fetch_assoc()['total'];



// Example Fee Data (Replace with actual DB queries)
$total_yearly = STUDENT_MONTHLY_FEES * $totalStudents;
$total_pending = $total_yearly - $total_collected;

$collected_percentage = ($total_yearly > 0) ? ($total_collected / $total_yearly) * 100 : 0;
$pending_percentage = ($total_yearly > 0) ? ($total_pending / $total_yearly) * 100 : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Page Content -->
        <div id="page-content-wrapper" class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button class="btn btn-primary"> Home </button>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="card p-3 text-center bg-danger text-white">
                        <h5>₹ <?php echo number_format($total_yearly) ?></h5>
                        <p>Total Fees</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center bg-success text-white">
                        <h5>₹ <?php echo number_format($total_collected); ?></h5>
                        <p>Collected</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center  bg-warning ">
                        <h5>₹ <?php echo number_format($total_pending); ?></h5>
                        <p>Pending</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center bg-primary text-white">
                        <h5><?php echo round($collected_percentage,2); ?></h5>
                        <p>Collected %</p>
                    </div>
                </div>
               
            </div>
            <div class= 'row mt-3'>
            <div class="col-md-3">
                    <div class="card p-3 text-center bg-primary text-white">
                        <h5><?php echo $totalStudents; ?></h5>
                        <p>Students Assigned</p>
                    </div>
                </div>
            </div>
            <!-- <div class="row mt-4">
                <div class="col-md-6">
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="overviewChart"></canvas>
                </div>
            </div> -->
        </div>
    </div>
    <!-- <div class="dropdown-menu dropdown-menu-right pt-0">
            <div class="dropdown-header bg-light py-2"><strong>Account</strong></div>
            <a class="dropdown-item" href="http://127.0.0.1:8000/user/profile">
                <i class="mfe-2  bi bi-person" style="font-size: 1.2rem;"></i> Profile
            </a>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mfe-2  bi bi-box-arrow-left" style="font-size: 1.2rem;"></i> Logout
            </a>
            <form id="logout-form" action="http://127.0.0.1:8000/logout" method="POST" class="d-none">
                <input type="hidden" name="_token" value="zHaPKeREQexPO5vanr4Z3WcxBw8RV7j4MUEEfWVe" autocomplete="off">            </form>
        </div> -->
    <script>
        // const ctx1 = document.getElementById('salesChart').getContext('2d');
        // new Chart(ctx1, {
        //     type: 'bar',
        //     data: {
        //         labels: ['05-02-25', '06-02-25', '07-02-25', '08-02-25', '09-02-25'],
        //         datasets: [{
        //             label: 'Sales',
        //             data: [0, 0, 0, 2.1, 0],
        //             backgroundColor: 'blue'
        //         }]
        //     }
        // });
        
        // const ctx2 = document.getElementById('overviewChart').getContext('2d');
        // new Chart(ctx2, {
        //     type: 'doughnut',
        //     data: {
        //         labels: ['Sales', 'Purchases', 'Expenses'],
        //         datasets: [{
        //             data: [80, 10, 10],
        //             backgroundColor: ['orange', 'blue', 'red']
        //         }]
        //     }
        // });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
