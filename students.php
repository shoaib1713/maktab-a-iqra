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

if (!empty($_GET['search_address'])) {
    $search_query .= " AND s.student_address LIKE ?";
    $params[] = "%" . $_GET['search_address'] . "%";
    $param_types .= "s";
}

if (!empty($_GET['search_class'])) {
    $search_query .= " AND s.class = ?";
    $params[] = $_GET['search_class'];
    $param_types .= "s";
}

if (!empty($_GET['search_class_time'])) {
    $search_query .= " AND s.class_time = ?";
    $params[] = $_GET['search_class_time'];
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
    <title>Students - MAKTAB-E-IQRA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pagination.css">
    <link rel="icon" href="assets/images/logo.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 rounded">
                <div class="container-fluid">
                    <button class="btn" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="navbar-brand ms-2">Student Management</span>
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2"><i class="fas fa-user-circle fs-5"></i> <?php echo $_SESSION['user_name']; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                                <li><a class="dropdown-item" href="modules/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Total Students: <span class="badge bg-primary"><?php echo $totalStudents; ?></span></h4>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                <i class="fas fa-tasks me-1"></i> Bulk Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#transferTeacherModal"><i class="fas fa-exchange-alt me-2"></i>Transfer to Teacher</a></li>
                                <li><a class="dropdown-item" href="#" id="bulkActivateBtn"><i class="fas fa-check-circle me-2"></i>Activate Selected</a></li>
                                <li><a class="dropdown-item" href="#" id="bulkDeactivateBtn"><i class="fas fa-times-circle me-2"></i>Deactivate Selected</a></li>
                            </ul>
                        </div>
                        <a href='add_student.php' class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Add Student
                        </a>
                    </div>
                </div>
                
                <!-- Search Panel -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Search Students</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="students.php" class="row g-3">
                            <!-- Search by Name -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="search_name" class="form-control" placeholder="Search by Name"
                                    value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
                            </div>

                            <!-- Search by Phone -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="search_phone" class="form-control" placeholder="Search by Phone"
                                    value="<?= htmlspecialchars($_GET['search_phone'] ?? '') ?>">
                            </div>

                            <!-- Search by Address -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="search_address" class="form-control" placeholder="Search by Address"
                                    value="<?= htmlspecialchars($_GET['search_address'] ?? '') ?>">
                            </div>

                            <!-- Search by Class Time -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Class Time</label>
                                <select name="search_class_time" class="form-select">
                                    <option value="">Select Class Time</option>
                                    <option value="Fajar 1st Class" <?= ($_GET['search_class_time'] ?? '') == 'Fajar 1st Class' ? 'selected' : '' ?>>Fajar 1st Class</option>
                                    <option value="Fajar 2nd Class" <?= ($_GET['search_class_time'] ?? '') == 'Fajar 2nd Class' ? 'selected' : '' ?>>Fajar 2nd Class</option>
                                    <option value="Asar 1st Class" <?= ($_GET['search_class_time'] ?? '') == 'Asar 1st Class' ? 'selected' : '' ?>>Asar 1st Class</option>
                                    <option value="Magrib 1st Class" <?= ($_GET['search_class_time'] ?? '') == 'Magrib 1st Class' ? 'selected' : '' ?>>Magrib 1st Class</option>
                                </select>
                            </div>

                            <!-- Search by Class -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Class</label>
                                <select name="search_class" class="form-select">
                                    <option value="">Select Class</option>
                                    <option value="1" <?= ($_GET['search_class'] ?? '') == '1' ? 'selected' : '' ?>>1</option>
                                    <option value="2" <?= ($_GET['search_class'] ?? '') == '2' ? 'selected' : '' ?>>2</option>
                                    <option value="3" <?= ($_GET['search_class'] ?? '') == '3' ? 'selected' : '' ?>>3</option>
                                    <option value="4" <?= ($_GET['search_class'] ?? '') == '4' ? 'selected' : '' ?>>4</option>
                                    <option value="5" <?= ($_GET['search_class'] ?? '') == '5' ? 'selected' : '' ?>>5</option>
                                    <option value="6" <?= ($_GET['search_class'] ?? '') == '6' ? 'selected' : '' ?>>6</option>
                                </select>
                            </div>

                            <!-- Search by Assigned Ulma -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Assigned Ulma</label>
                                <select name="search_teacher" class="form-select">
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
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Status</label>
                                <select name="search_status" class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="active" <?= ($_GET['search_status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= ($_GET['search_status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>

                            <!-- Search & Reset Buttons -->
                            <div class="col-12 d-flex">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                                <a href="students.php" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover border">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAllStudents">
                                            </div>
                                        </th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Class Time</th>
                                        <th>Yearly Fees</th>
                                        <th>Remarks</th>
                                        <th>Assigned Ulma</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-4">No students found</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php while ($row = $result->fetch_assoc()): 
                                            $statusBadge = $row['is_deleted'] ? '<span class="badge bg-danger">Inactive</span>' : '<span class="badge bg-success">Active</span>';
                                            $deleteRestore = $row['is_deleted'] 
                                                ? "<a href='restore_student.php?id={$row['id']}' class='btn btn-sm btn-info' title='Restore'><i class='fas fa-redo'></i></a>" 
                                                : "<a href='delete_student.php?id={$row['id']}' class='btn btn-sm btn-danger' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input student-checkbox" type="checkbox" value="<?= $row['id'] ?>">
                                                    </div>
                                                </td>
                                                <td><img src="<?= $row['photo']; ?>" class="student-photo" alt="Student"></td>
                                                <td><?= $row['name']; ?></td>
                                                <td><?= $row['class']; ?></td>
                                                <td><?= $row['phone']; ?></td>
                                                <td><?= $row['student_address']; ?></td>
                                                <td><?= $row['class_time']; ?></td>
                                                <td>₹ <?= number_format($row['annual_fees']); ?></td>
                                                <td><?= $row['remarks'] ?></td>
                                                <td><?= $row['teacher_name']; ?></td>
                                                <td><?= $statusBadge; ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href='edit_student.php?id=<?= $row['id']; ?>' class='btn btn-sm btn-primary' title='Edit'><i class='fas fa-edit'></i></a>
                                                        <?= $deleteRestore; ?>
                                                        <a href='student_history.php?id=<?= $row['id']; ?>' class='btn btn-sm btn-secondary' title='History'><i class='fas fa-history'></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($_GET['search_name']) ? '&search_name=' . urlencode($_GET['search_name']) : '' ?><?= !empty($_GET['search_phone']) ? '&search_phone=' . urlencode($_GET['search_phone']) : '' ?><?= !empty($_GET['search_address']) ? '&search_address=' . urlencode($_GET['search_address']) : '' ?><?= !empty($_GET['search_class_time']) ? '&search_class_time=' . urlencode($_GET['search_class_time']) : '' ?><?= !empty($_GET['search_teacher']) ? '&search_teacher=' . urlencode($_GET['search_teacher']) : '' ?><?= !empty($_GET['search_status']) ? '&search_status=' . urlencode($_GET['search_status']) : '' ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php 
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                $startPage = max(1, $endPage - 4);
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++): 
                            ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['search_name']) ? '&search_name=' . urlencode($_GET['search_name']) : '' ?><?= !empty($_GET['search_phone']) ? '&search_phone=' . urlencode($_GET['search_phone']) : '' ?><?= !empty($_GET['search_address']) ? '&search_address=' . urlencode($_GET['search_address']) : '' ?><?= !empty($_GET['search_class_time']) ? '&search_class_time=' . urlencode($_GET['search_class_time']) : '' ?><?= !empty($_GET['search_teacher']) ? '&search_teacher=' . urlencode($_GET['search_teacher']) : '' ?><?= !empty($_GET['search_status']) ? '&search_status=' . urlencode($_GET['search_status']) : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($_GET['search_name']) ? '&search_name=' . urlencode($_GET['search_name']) : '' ?><?= !empty($_GET['search_phone']) ? '&search_phone=' . urlencode($_GET['search_phone']) : '' ?><?= !empty($_GET['search_address']) ? '&search_address=' . urlencode($_GET['search_address']) : '' ?><?= !empty($_GET['search_class_time']) ? '&search_class_time=' . urlencode($_GET['search_class_time']) : '' ?><?= !empty($_GET['search_teacher']) ? '&search_teacher=' . urlencode($_GET['search_teacher']) : '' ?><?= !empty($_GET['search_status']) ? '&search_status=' . urlencode($_GET['search_status']) : '' ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Transfer Teacher Modal -->
    <div class="modal fade" id="transferTeacherModal" tabindex="-1" aria-labelledby="transferTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferTeacherModalLabel">Transfer Students to Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="transferTeacherForm">
                        <div class="mb-3">
                            <label for="newTeacher" class="form-label">Select Teacher</label>
                            <select class="form-select" id="newTeacher" name="newTeacher" required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Selected Students</label>
                            <div id="selectedStudentsList" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                <small class="text-muted">No students selected</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmTransfer">Transfer Students</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Sidebar toggle functionality
            const menuToggle = document.getElementById('menu-toggle');
            const sidebarWrapper = document.getElementById('sidebar-wrapper');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarWrapper.classList.toggle('toggled');
            });
            
            sidebarOverlay.addEventListener('click', function() {
                sidebarWrapper.classList.remove('toggled');
            });

            // Bulk operations functionality
            let selectedStudents = new Set();

            // Select all checkbox
            $('#selectAllStudents').change(function() {
                $('.student-checkbox').prop('checked', $(this).prop('checked'));
                updateSelectedStudents();
            });

            // Individual student checkbox
            $(document).on('change', '.student-checkbox', function() {
                updateSelectedStudents();
            });

            function updateSelectedStudents() {
                selectedStudents.clear();
                $('.student-checkbox:checked').each(function() {
                    selectedStudents.add($(this).val());
                });

                // Enable/disable bulk actions dropdown
                $('#bulkActionsDropdown').prop('disabled', selectedStudents.size === 0);

                // Update selected students list in modal
                updateSelectedStudentsList();
            }

            function updateSelectedStudentsList() {
                const list = $('#selectedStudentsList');
                if (selectedStudents.size === 0) {
                    list.html('<small class="text-muted">No students selected</small>');
                    return;
                }

                let html = '<ul class="list-unstyled mb-0">';
                $('.student-checkbox:checked').each(function() {
                    const studentId = $(this).val();
                    const studentName = $(this).closest('tr').find('td:eq(2)').text();
                    html += `<li>${studentName}</li>`;
                });
                html += '</ul>';
                list.html(html);
            }

            // Bulk activate/deactivate
            $('#bulkActivateBtn, #bulkDeactivateBtn').click(function(e) {
                e.preventDefault();
                if (selectedStudents.size === 0) return;

                const action = $(this).attr('id') === 'bulkActivateBtn' ? 'activate' : 'deactivate';
                const confirmMessage = `Are you sure you want to ${action} the selected students?`;

                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: 'bulk_update_students.php',
                        method: 'POST',
                        data: {
                            student_ids: Array.from(selectedStudents),
                            action: action
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('An error occurred while processing your request.');
                        }
                    });
                }
            });

            // Transfer teacher
            $('#confirmTransfer').click(function() {
                if (selectedStudents.size === 0) return;

                const newTeacherId = $('#newTeacher').val();
                if (!newTeacherId) {
                    alert('Please select a teacher');
                    return;
                }

                $.ajax({
                    url: 'bulk_transfer_students.php',
                    method: 'POST',
                    data: {
                        student_ids: Array.from(selectedStudents),
                        new_teacher_id: newTeacherId
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing your request.');
                    }
                });
            });
        });
    </script>
</body>
</html>
