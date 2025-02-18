<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$year = date("Y");

$startYear = $year - 1;
$endYear = $year;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['academic_year']) && $_POST['academic_year'] !== '') {
    $selectedYear = $_POST['academic_year'];
    list($startYear, $endYear) = explode("-", $selectedYear);
}
$startMonth = ACEDEMIC_START_MONTH;
$endMonth = ACEDEMIC_END_MONTH;

// Get total yearly fees
$sql_yearly = "SELECT SUM(s.annual_fees) AS total_yearly FROM students s LEFT JOIN student_status_history sst on sst.student_id = s.id WHERE sst.status in ('active','transferred') and (
    (sst.year = $startYear AND sst.month >= $startMonth)
    OR 
    (sst.year = $endYear AND sst.month <= $endMonth)
) and sst.current_active_record = 0";
$stmt = $conn->prepare($sql_yearly);
$stmt->execute();
$stmt->bind_result($total_yearly);
$stmt->fetch();
$stmt->close();

// Get total collected fees
$sql_collected = "SELECT SUM(amount) AS total_collected FROM fees WHERE status = 'paid' AND (
    (year = $startYear AND month >= $startMonth)
    OR 
    (year = $endYear AND month <= $endMonth)
)";
$stmt = $conn->prepare($sql_collected);
$stmt->execute();
$stmt->bind_result($total_collected);
$stmt->fetch();
$stmt->close();

$countQuery = "SELECT COUNT(*) as total FROM students";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalStudents = $countResult->fetch_assoc()['total'];

$total_pending = $total_yearly - $total_collected;
$collected_percentage = ($total_yearly > 0) ? ($total_collected / $total_yearly) * 100 : 0;
$pending_percentage = ($total_yearly > 0) ? ($total_pending / $total_yearly) * 100 : 0;

// Get collected fees percentage per teacher
$sql_teacher_fees = "SELECT 
    u.name, 
    COALESCE(SUM(sst.salana_fees), 0) AS total_fees,
    COALESCE(SUM(f.amount), 0) AS collected_fees
FROM users u 
LEFT JOIN students s ON u.id = s.assigned_teacher 

LEFT JOIN student_status_history sst 
    ON s.id = sst.student_id 
    AND sst.current_active_record = 0 
    AND ((sst.year = $startYear AND sst.month >= $startMonth) OR (sst.year = $endYear AND sst.month <= $endMonth))
    AND sst.status in ('active','transferred')

LEFT JOIN (
    SELECT student_id, SUM(amount) AS amount 
    FROM fees 
    WHERE status = 'paid' 
    AND ((year = $startYear AND month >= $startMonth) OR (year = $endYear AND month <= $endMonth))
    GROUP BY student_id
) f ON s.id = f.student_id 

WHERE u.role = 'teacher'
GROUP BY u.id";


$teacherStmt = $conn->prepare($sql_teacher_fees);
$teacherStmt->execute();
$teacherResult = $teacherStmt->get_result();
$teachers = [];
while ($row = $teacherResult->fetch_assoc()) {
    $teachers[] = $row;
}

//committe result
$startDate = $startYear.'-'.ACEDEMIC_START_MONTH.'-01';
$endDate = $endYear.'-'.ACEDEMIC_END_MONTH.'-31';
$committeeFeesQuery = "SELECT SUM(mfc.amount) as committee_fees FROM `meeting_details` md
LEFT JOIN `meeting_fees_collection` mfc ON mfc.meeting_id = md.id
WHERE meeting_date >= '$startDate' and meeting_date <= '$endDate'";

//echo $committeeFeesQuery; exit;

$stmt = $conn->prepare($committeeFeesQuery);
$stmt->execute();
$stmt->bind_result($committeeCollectedFess);
$stmt->fetch();
$stmt->close();

//maintenance result.
$maintenanceQuery = "SELECT SUM(amount) FROM `maintenance` WHERE ((year = $startYear AND month >= $startMonth) OR (year = $endYear AND month <= $endMonth))";

$stmt = $conn->prepare($maintenanceQuery);
$stmt->execute();
$stmt->bind_result($maintenanceResult);
$stmt->fetch();
$stmt->close();

//Available Balance
$availableBalance = ($total_collected + $committeeCollectedFess) - $maintenanceResult;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div id="page-content-wrapper" class="container-fluid">
            <marquee class="d-block w-100 text-secondary fw-bold">AZEEM O SHAAN SALANA JALSA & TAQSEEM E ASNAD MAKTAB-E-IQRA KA 3 RA JALSA RAMADAN KE BAD INSHALLAH</marquee>
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
                    <div class="card p-3 text-center text-bg-dark">
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
                    <div class="card p-3 text-center bg-danger cursor-pointer" id="pendingBox">
                        <h5>₹ <?php echo number_format($total_pending); ?></h5>
                        <p>Pending</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center bg-primary text-white">
                        <h5><?php echo round($collected_percentage,2); ?>%</h5>
                        <p>Collected %</p>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                 <div class="col-md-4">
                    <div class="card p-3 text-center text-bg-light">
                        <h5><?php echo number_format($committeeCollectedFess); ?></h5>
                        <p>Committee Collected Amount</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 text-center text-bg-info">
                        <h5><?php echo number_format($maintenanceResult); ?></h5>
                        <p>Maintenance</p>
                    </div>
                </div>
                   <div class="col-md-4">
                    <div class="card p-3 text-center text-bg-secondary">
                        <h5><?php echo number_format($availableBalance); ?></h5>
                        <p>Available Balance</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <h4>Collected Fees Percentage Per Teacher</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Collected Fees</th>
                                <th>Total Fees</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $row): ?>
                                <tr>
                                    <td><?php echo $row['name']; ?></td>
                                    <td>₹ <?php echo number_format($row['collected_fees']); ?></td>
                                    <td>₹ <?php echo number_format($row['total_fees']); ?></td>
                                    <td><?php echo ($row['total_fees'] > 0) ? round(($row['collected_fees'] / $row['total_fees']) * 100, 2) : 0; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <canvas id="feesChart"></canvas>
                         <script>
                        var ctx = document.getElementById('feesChart').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode(array_column($teachers, 'name')); ?>,
                                datasets: [{
                                    label: 'Collected Fees %',
                                    data: <?php echo json_encode(array_map(fn($t) => ($t['total_fees'] > 0) ? round(($t['collected_fees'] / $t['total_fees']) * 100, 2) : 0, $teachers)); ?>,
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                                }]
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>