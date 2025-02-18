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
                $query = "UPDATE students SET name=?, class=?, annual_fees=?, phone=?, assigned_teacher=?, photo=? WHERE id=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssisi", $name, $class, $annual_fees, $phone, $teacher_id, $targetFilePath, $id);
            } else {
                echo "<p style='color:red;'>Error uploading photo.</p>";
            }
        } else {
            echo "<p style='color:red;'>Invalid file format. Please upload JPG or PNG.</p>";
        }
    } else {
        // Update student without changing photo
        $query = "UPDATE students SET name=?, class=?, annual_fees=?, phone=?, assigned_teacher=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssii", $name, $class, $annual_fees, $phone, $teacher_id, $id);
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
    <title>Add Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a href = "students.php" class="btn btn-primary">Back</a>
                <div class="d-flex align-items-center">
                    <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                    <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <h2 class="mb-4">Add Student</h2>
            <div class="card p-4 shadow-sm">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">👤 Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $student['name'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">📸 Photo</label>
                        <input type="file" name="photo" class="form-control">
                        <br>
                        <img src="<?= $student['photo']; ?>" alt="Student Photo" class="img-thumbnail" width="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🏫 Class</label>
                        <select name="class" class="form-select" required>
                            <option value="">Select Class</option>
                            <option value="1" <?= $student['class'] == 1 ? 'selected' : '' ?>>Class 1</option>
                            <option value="2" <?= $student['class'] == 2 ? 'selected' : '' ?>>Class 2</option>
                            <option value="3" <?= $student['class'] == 3 ? 'selected' : '' ?>>Class 3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">📞 Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter phone number" value="<?= $student['phone'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">👨‍🏫 Assigned Teacher</label>
                        <select name="assigned_teacher" class="form-select" required>
                            <?php
                            $teacherQuery = "SELECT id, name FROM users WHERE role = 'teacher' ";
                            $teacherResult = mysqli_query($conn, $teacherQuery);
                            while ($teacher = mysqli_fetch_assoc($teacherResult)) {
                                $selected = ($teacher['id'] == $student['assigned_teacher']) ? "selected" : "";
                                echo "<option value='{$teacher['id']}' $selected>{$teacher['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">👤 Full Name</label>
                        <input type="text" name="annual_fees" class="form-control" value="<?= $student['annual_fees'] ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Student</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


