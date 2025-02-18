<?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            require_once 'config.php';
            require 'config/db.php';

            if (!isset($_SESSION['user_name'])) {
                header("Location: login.php");
                exit();
            }

            $user_role = $_SESSION['role']; // 'admin' or 'teacher'
            $user_id = $_SESSION['user_id'];

            // Define academic year range dynamically
            $year = date("Y");
            $startYear = $year - 1;
            $endYear = $year;
            $startMonth = ACEDEMIC_START_MONTH;
            $endMonth = ACEDEMIC_END_MONTH;

            // Fetch students based on role
            $query = "SELECT 
                s.id,
                u.name as teacher_name, 
                s.name, 
                s.class, 
                s.phone, 
                COALESCE(SUM(f.amount), 0) AS paid_amount 
            FROM students s 
            LEFT JOIN fees f ON s.id = f.student_id 
                AND ( (f.year = ? AND f.month >= ?) OR (f.year = ? AND f.month <= ?) )  and f.status = 'paid'
            LEFT JOIN users u ON u.id = s.assigned_teacher";

            if ($user_role == 'teacher') {
                $query .= " WHERE s.assigned_teacher = ?";
            }

            $query .= " GROUP BY s.id, s.name, s.class, s.phone";

            $stmt = $conn->prepare($query);
            if ($user_role == 'teacher') {
                $stmt->bind_param("iiiii", $startYear, $startMonth, $endYear, $endMonth, $user_id);
            } else {
                $stmt->bind_param("iiii", $startYear, $startMonth, $endYear, $endMonth);
            }
            $stmt->execute();
            $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Fees Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div id="page-content-wrapper" class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href='pending_fees.php' class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <h2 class="mb-4">Students Fees Details</h2>

                <br>
               <div class="table-responsive">
                    <table id="pendingFeesTable" class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Phone</th>
                                <th>Paid Amount</th>
                                <th>Pending Amount</th>
                                <th>Assigned Ulma</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): 
                                $pending_amount = STUDENT_MONTHLY_FEES - $row['paid_amount'];
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['class'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['paid_amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pending_amount, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['teacher_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

    <script>
        $(document).ready(function () {
            $('#pendingFeesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: 'Download CSV',
                        className: 'btn btn-success'
                    }
                ]
            });
        });
    </script>
</body>
</html>