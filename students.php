<?php
session_start();
require 'config/db.php';



// Get logged-in user details
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // 'admin' or 'teacher'

$teacherQuery = $conn->query("SELECT id, name FROM users where role='teacher'");
$teachers = $teacherQuery->fetch_all(MYSQLI_ASSOC);

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search Filters
$search_query = "";
$params = [];
$param_types = "";

// Base Query
$query = "SELECT s.*, u.name as teacher_name FROM students s 
          LEFT JOIN users u ON u.id = s.assigned_teacher 
          WHERE 1=1";

if ($user_role == 'teacher') {
    $query .= " AND s.assigned_teacher = ?";
    $params[] = $user_id;
    $param_types .= "i";
}

// Check if search filters exist
if (!empty($_GET['search_name'])) {
    $search_query .= " AND s.name LIKE ?";
    $params[] = "%" . $_GET['search_name'] . "%";
    $param_types .= "s";
}

if (!empty($_GET['search_phone'])) {
    $search_query .= " AND s.phone LIKE ?";
    $params[] = "%" . $_GET['search_phone'] . "%";
    $param_types .= "s";
}

if (!empty($_GET['search_teacher'])) {
    $search_query .= " AND s.assigned_teacher = ?";
    $params[] = $_GET['search_teacher'];
    $param_types .= "i";
}

if (!empty($_GET['search_status'])) {
    $search_query .= " AND s.is_deleted = ?";
    $params[] = ($_GET['search_status'] == "active") ? 0 : 1;
    $param_types .= "i";
}





// Append search conditions
$query .= $search_query;
$query .= " LIMIT ?, ?";


// Add pagination parameters
$params[] = $offset;
$params[] = $limit;
$param_types .= "ii";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!empty($param_types)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();


$countQuery = "SELECT COUNT(*) as total FROM students s WHERE 1=1 " . $search_query;

if ($user_role == 'teacher') {
    $countQuery .= " AND s.assigned_teacher = ?";
}
$countStmt = $conn->prepare($countQuery);

// Extract search parameters (excluding pagination params)
$search_params = array_slice($params, 0, -2);
$search_types = substr($param_types, 0, -2);


// Bind only if parameters exist
if (!empty($search_params)) {
    $countStmt->bind_param($search_types, ...$search_params);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalStudents = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalStudents / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/pagination.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div id="page-content-wrapper" class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href='students.php' class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <h3 class="mb-4">Total Student : <?php echo $totalStudents; ?></h3>
                <a href='add_student.php' class="btn btn-primary mb-3">Add Student +</a>
                
                <!-- Search Form -->
                <div class="container mt-3">
                <form method="GET" action="students.php" class="row g-2">
                    <!-- Search by Name -->
                    <div class="col-md-3">
                        <input type="text" name="search_name" class="form-control" placeholder="Search by Name"
                            value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
                    </div>

                    <!-- Search by Phone -->
                    <div class="col-md-3">
                        <input type="text" name="search_phone" class="form-control" placeholder="Search by Phone"
                            value="<?= htmlspecialchars($_GET['search_phone'] ?? '') ?>">
                    </div>

                    <!-- Search by Assigned Ulma -->
                    <div class="col-md-3">
                        <select name="search_teacher" class="form-control">
                            <option value="">Select Ulma</option>
                            <?php
                            foreach ($teachers as $teacher) {
                                $selected = ($_GET['search_teacher'] ?? '') == $teacher['id'] ? 'selected' : '';
                                echo "<option value='{$teacher['id']}' {$selected}>{$teacher['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Search by Status -->
                    <div class="col-md-3">
                        <select name="search_status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="active" <?= ($_GET['search_status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($_GET['search_status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Search & Reset Buttons -->
                    <div class="col-md-3 d-flex">
                        <button type="submit" class="btn btn-primary me-2">Search</button>
                        <a href="students.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

                <br>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Phone</th>
                            <th>Salana Fees</th>
                            <th>Assigned Ulma</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { 
                               $deleteRestore = $row['is_deleted'] ? "<a href='restore_student.php?id={$row['id']}' class='btn btn-sm btn-info' title='Restore'>🔄</a>" : "<a href='delete_student.php?id={$row['id']}' class='btn btn-sm btn-danger'  title = 'Delete' onclick='return confirm(\"Are you sure?\")'>🗑</a>";
                        ?>
                            <tr>
                                <td><img src="<?= $row['photo']; ?>" width="50"></td>
                                <td><?= $row['name']; ?></td>
                                <td><?= $row['class']; ?></td>
                                <td><?= $row['phone']; ?></td>
                                <td><?= $row['annual_fees']; ?></td>
                                <td><?= $row['teacher_name']; ?></td>
                                <td><?= $row['is_deleted'] ? 'Inactive' : 'Active'; ?></td>
                                
                                <td>
                                <?php if( $row['is_deleted']== 0) {?> <a href='edit_student.php?id=<?= $row['id']; ?>' class='btn btn-sm btn-primary'>Edit</a> <?php } ?>
                                <?php echo $deleteRestore; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo; Previous</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 5);
                        $end = min($totalPages, $page + 5);

                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">Next &raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</body>
</html>
