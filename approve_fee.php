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

        function getUserName($userId, $conn) {
            if (!$userId) return "N/A"; // If no user assigned, return "N/A"
            
            $query = $conn->query("SELECT name FROM users WHERE id = '$userId' LIMIT 1");
            $user = $query->fetch_assoc();
            
            return $user ? $user['name'] : "Unknown";
        }
    $query = "SELECT f.*,s.name as student_name, s.phone FROM fees f LEFT JOIN students s ON s.id = f.student_id WHERE f.status = 'pending'";
    $result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Approval</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <div class="container-fluid p-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href="dashboard.php" class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            <h3 class="mb-4">Pending Fee Approvals</h3>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Fees Collected By</th>
                        <th>Amount</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Date</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr id="row_<?php echo $row['id']; ?>">
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?= getUserName($row['created_by'], $conn); ?></td>
                        <td>₹ <?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo date("F", mktime(0, 0, 0, $row['month'], 1)); ?></td>
                        <td><?php echo $row['Year']; ?></td>
                        <td><?php echo date('d-m-Y h:m:i',strtotime($row['created_at'])); ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>
                            <button class="btn btn-success btn-sm approve-btn" data-id="<?php echo $row['id']; ?>">
                                ✅ Approve
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $(".approve-btn").click(function() {
            var fee_id = $(this).data("id");
            var row = $("#row_" + fee_id);

            $.ajax({
                url: "update_table.php",
                type: "POST",
                data: {
                    action: "approve_fee",
                    fee_id: fee_id
                },
                success: function(response) {
                    response = response.trim()
                    if (response == "success") {
                        row.fadeOut("slow", function() {
                            $(this).remove();
                        });
                    } else {
                        alert("Failed to approve fee.");
                    }
                }
            });
        });
    });
    </script>
</body>

</html>