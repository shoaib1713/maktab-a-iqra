<?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            require_once 'config.php';
            require 'config/db.php';

            function getUserName($userId, $conn) {
                if (!$userId) return "N/A"; // If no user assigned, return "N/A"
                
                $query = $conn->query("SELECT name FROM users WHERE id = '$userId' LIMIT 1");
                $user = $query->fetch_assoc();
                
                return $user ? $user['name'] : "Unknown";
            }
            $searchQuery = "";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $year = $_POST['year'] ?? null;
                $month = $_POST['month'] ?? null;
            
                if (!$year && !$month) {
                    echo "<script>alert('Select Year or Month')</script>";
                } else {
                    $conditions = [];
                    if ($year) $conditions[] = "cheque_year = '$year'";
                    if ($month) $conditions[] = "cheque_month = '$month'";
            
                    $searchQuery = "WHERE " . implode(" AND ", $conditions);
                }
            }            

            // Pagination setup
            $limit = 10; // Number of records per page
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Fetch maintenance records with pagination
            $sql = "SELECT * FROM cheque_details 
            $searchQuery
            ORDER BY created_on DESC LIMIT $limit OFFSET $offset";
            $result = $conn->query($sql);

            // Get total records for pagination
            $total_records = $conn->query("SELECT COUNT(*) AS total FROM cheque_details $searchQuery")->fetch_assoc()['total'];
            $total_pages = ceil($total_records / $limit);
            ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/pagination.css">
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href='cheque_details.php' class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            <h3 class="mb-4">Cheque Details</h3>

            <!-- Search Form -->
            <form method="POST" class="row mb-3">
                <div class="col-md-4">
                    <label>Year:</label>
                    <select name="year" class="form-control">
                        <option value=''>Select Year</option>
                        <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                        <option value="<?php echo $y; ?>"
                            <?php echo ( isset($_POST['year']) && $_POST['year'] == $y) ? 'selected' : '' ?>>
                            <?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Month:</label>
                    <select name="month" class="form-control">
                        <option value=''>Select Month</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>"
                            <?php echo (isset($_POST['month']) && $_POST['month']==$m)?'selected':''?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
            <div class="col-md-4">
                <a href="add_cheque_details.php" class="btn btn-success">Add Cheque Details</a>
            </div>
            <!-- Meeting List -->
            <table id="meetingTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Cheque given Date</th>
                        <th>Cheque Number</th>
                        <th>Amount</th>
                        <th>Cheque Year</th>
                        <th>Cheque Month</th>
                        <th>Cheque handover Ulma</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { 
                            $deletetd = $row['is_deleted']==0 ? "
                                    <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>
                                        <i class='fas fa-trash-alt'></i>Delete
                                    </button>
                                ":'Deleted'; ?>
                    <tr>
                        <td>
                            <img src="<?php echo $row['cheque_photo']; ?>" alt="Cheque Photo" class="img-thumbnail"
                                width="50" data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="showImage('<?php echo $row['cheque_photo']; ?>')">
                        </td>
                        <td><?php echo date("d-m-Y", strtotime($row['cheque_given_date'])); ?></td>
                        <td><?php echo $row['cheque_number']; ?></td>
                        <td>₹ <?php echo number_format($row['cheque_amount'], 2); ?></td>
                        <td> <?php echo $row['cheque_year']; ?></td>
                        <td><?php echo date("F", mktime(0, 0, 0, $row['cheque_month'], 1)); ?></td>
                        <td><?php echo getUserName($row['cheque_handover_teacher'],$conn); ?></td>
                        <td> <?php echo $deletetd; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1) { ?>
                <a href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
                <?php } ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
                <?php } ?>

                <?php if ($page < $total_pages) { ?>
                <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                <?php } ?>
            </div>
        </div>
</body>

<!-- Bootstrap Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Cheque Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    onclick="closeModal()"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS (Ensure it's included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Declare myModal globally
let myModal = new bootstrap.Modal(document.getElementById("imageModal"));

function showImage(src) {
    document.getElementById("modalImage").src = src;
    myModal.show();
}

// Ensure modal is properly closed
document.getElementById("imageModal").addEventListener("hidden.bs.modal", function() {
    document.getElementById("modalImage").src = ""; // Clear image to prevent memory leaks
});

$(document).ready(function() {
    $(".delete-btn").click(function() {
        var id = $(this).data("id");
        var row = $("#row_" + id);

        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: "delete_maintenance.php",
                type: "POST",
                data: {
                    id: id
                },
                success: function(response) {
                    response = response.trim(); // Remove whitespace
                    if (response == "success") {
                        alert("Records deleted successfully");
                        window.location.href = 'maintenance_list.php';
                    } else {
                        alert("Failed to delete the record.");
                    }
                }
            });
        }
    });
});
</script>