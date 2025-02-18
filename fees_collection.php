<?php
session_start();
require_once 'config.php';
require 'config/db.php';

$startMonth = ACEDEMIC_START_MONTH;
$endMonth = ACEDEMIC_END_MONTH;

$year = date("Y");
$startYear = $year - 1;
$endYear = $year;

$students = $conn->query("SELECT id, name, phone,annual_fees FROM students WHERE is_deleted = 0");
$studentsArray = $students->fetch_all(MYSQLI_ASSOC); // Convert result to array

$studentPayments = [];

// Fetch Payment History if Student is Selected
    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        $historyQuery = $conn->prepare("SELECT amount, month, year, created_at FROM fees WHERE student_id = ? ORDER BY created_at DESC");
        $historyQuery->bind_param("i", $student_id);
        $historyQuery->execute();
        $studentPayments = $historyQuery->get_result()->fetch_all(MYSQLI_ASSOC);

        $filteredPayments = array_filter($studentPayments, function ($payment) use ($startYear, $endYear, $startMonth, $endMonth) {
            return 
                ($payment['year'] == $startYear && $payment['month'] >= $startMonth) ||  
                ($payment['year'] == $endYear && $payment['month'] <= $endMonth);
        });

        $intCollectedFeesOfStudentTillNow = array_reduce($filteredPayments, function ($sum, $payment) {
            return $sum + $payment['amount'];
        }, 0);

        $studentRecord = array_filter($studentsArray, function($student) use ($student_id) {
            return $student['id'] == $student_id;
        });
        $studentRecord = reset($studentRecord);
        $intTotalFeesOfStudent = $studentRecord['annual_fees'];
        $intCollectedFeesOfStudentTillNow += isset($_POST['amount'])?$_POST['amount']:0;

        if($intCollectedFeesOfStudentTillNow > $intTotalFeesOfStudent){
        echo "<script>
            alert('{$studentRecord['name']} Total Fee for the Year: $startYear and $endYear is collected');
            window.location.href = 'fees_collection.php'; // Refreshes the page after alert
        </script>";
        } 

    }

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $created_by = $_SESSION['user_id']; // Logged-in user
    $status = 'pending';

    //validate paid and pending fees should not be greater then salana fee for that student for the year.
    // Insert new fees record
    $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, month, year, created_by,status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidiis", $student_id, $amount, $month, $year, $created_by,$status);
    
    if ($stmt->execute()) {
        $success = "Fees added successfully!";
    } else {
        $error = "Error adding fees!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Collection - Maktab-a-Ekra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Page Content -->
        <div id="page-content-wrapper" class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href = 'fees_collection.php' class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <h2 class="mb-4">💰 Fees Collection</h2>
                
                <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                <div class="row">
                    <!-- Left Side: Form -->
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h4>Collect Fees</h4>
                            <form action="fees_collection.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Search Student</label>
                                    <input type="text" id="search" class="form-control" placeholder="Search by name or phone" onkeyup="filterStudents()">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Student</label>
                                    <select name="student_id" id="studentList" class="form-control" required onchange="loadStudentHistory(this.value)">
                                        <option value="">Select a student</option>
                                        <?php foreach ($studentsArray as $row) { ?>
                                            <option value="<?= $row['id'] ?>" <?= (isset($_GET['student_id']) && $_GET['student_id'] == $row['id']) ? 'selected' : '' ?>>
                                                <?= $row['name'] ?> (<?= $row['phone'] ?>)
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" name="amount" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Month</label>
                                    <select name="month" class="form-control" required>
                                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                                            <option value="<?= $i ?>"><?= date("F", mktime(0, 0, 0, $i, 1)) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Year</label>
                                    <select name="year" class="form-control" required>
                                        <?php for ($y = date("Y") - 5; $y <= date("Y"); $y++) { ?>
                                            <option value="<?= $y ?>"><?= $y ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Collect Fees</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Right Side: Payment History -->
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h4>📜 Payment History</h4>
                            <?php if (!empty($studentPayments)) { ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Amount</th>
                                            <th>Month</th>
                                            <th>Year</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($studentPayments as $payment) { ?>
                                            <tr>
                                                <td>₹<?= $payment['amount'] ?></td>
                                                <td><?= date("F", mktime(0, 0, 0, $payment['month'], 1)) ?></td>
                                                <td><?= $payment['year'] ?></td>
                                                <td><?= date("d M, Y", strtotime($payment['created_at'])) ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p class="text-muted">No payments found for this student.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterStudents() {
            let input = document.getElementById("search").value.toLowerCase();
            let options = document.getElementById("studentList").options;
            for (let i = 1; i < options.length; i++) {
                let text = options[i].text.toLowerCase();
                options[i].style.display = text.includes(input) ? "block" : "none";
            }
        }
        
        function loadStudentHistory(studentId) {
            if (studentId) {
                window.location.href = 'fees_collection.php?student_id=' + studentId;
            }
        }
    </script>
</body>
</html>
