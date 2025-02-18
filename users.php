<?php
require_once 'config.php';
require 'config/db.php';

$limit = 10; // Users per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch users with pagination
$result = $conn->query("SELECT * FROM users LIMIT $limit OFFSET $offset");
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="bg-dark text-white">
        <?php include 'includes/sidebar.php'; ?>
        </div>
        <!-- Page Content -->
        <div id="page-content-wrapper" class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href='users.php' class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                    <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                    <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <h2 class="mb-4">Users List</h2>
                <a href="add_user.php" class="btn btn-primary mb-3">Add User +</a>
                <table class="table table-bordered" id="studentTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>                            
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentBody">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td>
                            <?php if ($row['is_deleted'] == 1) { ?>
                                <span class="status deleted">Deleted</span>
                            <?php } else { ?>
                                <span class="status active">Active</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?= $row['id'] ?>" class='btn btn-sm btn-primary'>✏️</a>
                            <?php if ($row['is_deleted'] == 0) { ?>
                                <a href="delete_user.php?id=<?= $row['id'] ?>" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure?')">🗑️</a>
                            <?php } else { ?>
                                <a href="restore_user.php?id=<?= $row['id'] ?>" class='btn btn-sm btn-info'>🔄</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <nav>
                    <ul class="pagination" id="pagination">
                        <li class="page-item disabled" id="prevPage">
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <a href="users.php?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
                        <?php } ?>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let rowsPerPage = 1;
            let rows = document.querySelectorAll("#studentBody tr");
            let totalPages = Math.ceil(rows.length / rowsPerPage);
            let currentPage = 1;

            function showPage(page) {
                let start = (page - 1) * rowsPerPage;
                let end = start + rowsPerPage;
                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? "table-row" : "none";
                });
                document.getElementById("prevPage").classList.toggle("disabled", page === 1);
                document.getElementById("nextPage").classList.toggle("disabled", page === totalPages);
            }

            document.getElementById("prevPage").addEventListener("click", function (e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            document.getElementById("nextPage").addEventListener("click", function (e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });

            showPage(currentPage);
        });
    </script>
</body>
</html>
