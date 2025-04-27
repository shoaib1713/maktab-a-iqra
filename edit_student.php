<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id'];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}

include 'student_history.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $class = $_POST['class'];
    $annual_fees = $_POST['annual_fees'];
    $phone = $_POST['phone'];
    $student_address = $_POST['student_address'];
    $class_time = $_POST['class_time'];
    $remarks = $_POST['remarks'];
    $teacher_id = $_POST['assigned_teacher'];
    $id = $_GET['id'];
    
    // Handle File Upload
    if (!empty($_FILES["photo"]["name"])) {
        $targetDir = "assets/images/";
        $fileName = basename($_FILES["photo"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        
        // Allow only specific file formats
        $allowedTypes = array("jpg", "jpeg", "png");
        if (in_array($fileType, $allowedTypes)) {

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                // Update student with new photo
                $query = "UPDATE students SET name=?, class=?, annual_fees=?, phone=?, student_address=?, class_time=?, remarks=?, assigned_teacher=?, photo=? WHERE id=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssssssssi", $name, $class, $annual_fees, $phone, $student_address, $class_time, $remarks, $teacher_id, $targetFilePath, $id);
            } else {
                echo "<p style='color:red;'>Error uploading photo.</p>";
            }
        } else {
            echo "<p style='color:red;'>Invalid file format. Please upload JPG or PNG.</p>";
        }
    } else {
        // Update student without changing photo
        $query = "UPDATE students SET name=?, class=?, annual_fees=?, phone=?, student_address=?, class_time=?, remarks=?, assigned_teacher=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssii", $name, $class, $annual_fees, $phone, $student_address, $class_time, $remarks, $teacher_id, $id);
    }

    if ($stmt->execute()) {
        updateStudentStatusHistory($conn,$user_id, $id,date('Y'),$teacher_id,$annual_fees);
        header("Location: students.php");
        exit();
    } else {
        echo "<p style='color:red;'>Error updating student.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - MAKTAB-E-IQRA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/logo.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 rounded">
            <div class="container-fluid">
                <button class="btn" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand ms-2">Edit Student</span>
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
                <h4 class="mb-0">Edit Student: <?= $student['name'] ?></h4>
                <a href="students.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Students
                </a>
            </div>

            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <img src="<?= $student['photo']; ?>" alt="Student Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 class="mb-0"><?= $student['name'] ?></h5>
                            <p class="text-muted small mb-0">Class <?= $student['class'] ?></p>
                            <p class="text-muted small mb-3"><?= $student['phone'] ?></p>
                            
                            <div class="d-grid">
                                <a href="student_history.php?id=<?= $student['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-history me-1"></i> View History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Update Student Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="fas fa-user me-1"></i> Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control" value="<?= $student['name'] ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="fas fa-phone me-1"></i> Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="phone" class="form-control" value="<?= $student['phone'] ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i> Student Address <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" name="student_address" rows="2" required><?= $student['student_address'] ?></textarea>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-clock me-1"></i> Class Time <span class="text-danger">*</span>
                                    </label>
                                    <select name="class_time" class="form-select" required>
                                        <option value="">Select Class Time</option>
                                        <option value="Fajar 1st Class" <?= $student['class_time'] == 'Fajar 1st Class' ? 'selected' : '' ?>>Fajar 1st Class</option>
                                        <option value="Fajar 2nd Class" <?= $student['class_time'] == 'Fajar 2nd Class' ? 'selected' : '' ?>>Fajar 2nd Class</option>
                                        <option value="Asar 1st Class" <?= $student['class_time'] == 'Asar 1st Class' ? 'selected' : '' ?>>Asar 1st Class</option>
                                        <option value="Magrib 1st Class" <?= $student['class_time'] == 'Magrib 1st Class' ? 'selected' : '' ?>>Magrib 1st Class</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-chalkboard-teacher me-1"></i> Assigned Teacher <span class="text-danger">*</span>
                                    </label>
                                    <select name="assigned_teacher" class="form-select" required>
                                        <option value="">Select Teacher</option>
                                        <?php
                                        $teacherQuery = "SELECT id, name FROM users WHERE role = 'teacher'";
                                        $teacherResult = mysqli_query($conn, $teacherQuery);
                                        while ($teacher = mysqli_fetch_assoc($teacherResult)) {
                                            $selected = ($teacher['id'] == $student['assigned_teacher']) ? "selected" : "";
                                            echo "<option value='{$teacher['id']}' $selected>{$teacher['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-rupee-sign me-1"></i> Salana Fees <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="annual_fees" class="form-control" value="<?= $student['annual_fees'] ?>" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-graduation-cap me-1"></i> Class <span class="text-danger">*</span>
                                    </label>
                                    <select name="class" class="form-select" required>
                                        <option value="">Select Class</option>
                                        <option value="1" <?= $student['class'] == '1' ? 'selected' : '' ?>>1</option>
                                        <option value="2" <?= $student['class'] == '2' ? 'selected' : '' ?>>2</option>
                                        <option value="3" <?= $student['class'] == '3' ? 'selected' : '' ?>>3</option>
                                        <option value="4" <?= $student['class'] == '4' ? 'selected' : '' ?>>4</option>
                                        <option value="5" <?= $student['class'] == '5' ? 'selected' : '' ?>>5</option>
                                        <option value="6" <?= $student['class'] == '6' ? 'selected' : '' ?>>6</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">
                                        <i class="fas fa-camera me-1"></i> Photo
                                    </label>
                                    <input type="file" name="photo" class="form-control">
                                    <div class="form-text">Only upload a new photo if you want to change the existing one. Allowed formats: JPG, JPEG, PNG.</div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">
                                        <i class="fas fa-comment me-1"></i> Remarks
                                    </label>
                                    <textarea class="form-control" name="remarks" rows="3"><?= $student['remarks'] ?></textarea>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="students.php" class="btn btn-light px-4">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-1"></i> Update Student
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
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
        
        // Preview image before upload
        document.querySelector('input[name="photo"]').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.rounded-circle').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    });
</script>
</body>
</html>


