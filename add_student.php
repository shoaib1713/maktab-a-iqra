<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id'];
// Fetch teachers from the database
$teacherQuery = $conn->query("SELECT id, name FROM users where role='teacher'");
$teachers = $teacherQuery->fetch_all(MYSQLI_ASSOC);

include 'student_history.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $class = $_POST['class'];
    $annual_fees = $_POST['annual_fees'];
    $phone = $_POST['phone'];
    $teacher_id = $_POST['assigned_teacher'];

    // File Upload Handling
    $target_dir = "assets/images/";
    $photo_name = basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . time() . "_" . $photo_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is valid
    if (isset($_FILES["photo"])) {
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }
    }

    // Allow only certain file formats
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        die("Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        // Insert student data into database
        $query = "INSERT INTO students (name, photo, class, annual_fees, phone, assigned_teacher) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $name, $target_file, $class, $annual_fees, $phone, $teacher_id);
        if ($stmt->execute()) {
            $student_id = $stmt->insert_id; // Get last inserted meeting ID
            updateStudentStatusHistory($conn,$user_id, $student_id,date('Y'),$teacher_id,$annual_fees,'active');
            echo "Student added successfully!";
            header("Location: students.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "File upload failed.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Form</title>
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
                    <a href = 'students.php' class="btn btn-primary">Back</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <h2 class="mb-4">Add Student</h2>
                <form action="add_student.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo *</label>
                        <input type="file" class="form-control" id="photo" name="photo" required>
                    </div>
                    <div class="mb-3">
                        <label for="class" class="form-label">Class *</label>
                        <input type="text" class="form-control" id="class" name="class" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone *</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                    <label for="status" class="form-label">Assigned Ulma</label>
                        <select class="form-control" id="assigned_teacher" name="assigned_teacher" required>
                          <option value="">Select Ulma</option>
                          <?php foreach($teachers as $teacher){?>
                            <option value="<?= $teacher['id']; ?>"><?= $teacher['name']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-control" id="status" name="is_deleted" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <!-- give edit option to only sadar and admin -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Salana Fees *</label>
                        <input type="number" class="form-control" id="annual_fees" value = '2000' name="annual_fees" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

