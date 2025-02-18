<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require 'config/db.php';

// Fetch users for dropdowns
$teachers = $conn->query("SELECT id, name FROM users WHERE role = 'teacher'");
$admins = $conn->query("SELECT id, name FROM users WHERE role = 'admin'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meeting_date = $_POST['meeting_date'] ?? date('Y-m-d'); // Use selected date or default to today
    $student_responsibility = $_POST['student_responsibility'];
    $namaz_responsibility = $_POST['namaz_responsibility'];
    $visit_fajar = $_POST['visit_fajar'];
    $visit_asar = $_POST['visit_asar'];
    $visit_magrib = $_POST['visit_magrib'];
    $maktab_lock = $_POST['maktab_lock'];
    $cleanliness_ethics = $_POST['cleanliness_ethics'];
    $food_responsibility = $_POST['food_responsibility'];
// echo "<pre>";
//     var_dump($_POST); exit;
    // Insert meeting details
    $stmt = $conn->prepare("INSERT INTO meeting_details (meeting_date,student_responsibility, namaz_responsibility, visit_fajar, visit_asar, visit_magrib, maktab_lock, cleanliness_ethics, food_responsibility) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiiiiii", $meeting_date,$student_responsibility, $namaz_responsibility, $visit_fajar, $visit_asar, $visit_magrib, $maktab_lock, $cleanliness_ethics, $food_responsibility);
    $stmt->execute();
    $meeting_id = $stmt->insert_id; // Get last inserted meeting ID
    $stmt->close();

    // Insert fees collection records
    if (!empty($_POST['fees_admin']) && !empty($_POST['fees_amount'])) {
        $stmt = $conn->prepare("INSERT INTO meeting_fees_collection (meeting_id, admin_id, amount) VALUES (?, ?, ?)");

        for ($i = 0; $i < count($_POST['fees_admin']); $i++) {
            $admin_id = $_POST['fees_admin'][$i];
            $amount = $_POST['fees_amount'][$i];

            if (!empty($admin_id) && !empty($amount)) {
                $stmt->bind_param("iid", $meeting_id, $admin_id, $amount);
                $stmt->execute();
            }
        }

        $stmt->close();
    }

    $conn->close();
    header("Location: meeting_list.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Meeting Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>

        <div class="container p-4">
            <h2 class="mb-4">Add Meeting Details</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Meeting Date:</label>
                    <input type="date" name="meeting_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Student Responsibility:</label>
                    <select name="student_responsibility" class="form-select" required>
                        <option value="">Select Teacher</option>
                        <?php while ($teacher = $teachers->fetch_assoc()): ?>
                            <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Namaz Responsibility:</label>
                    <select name="namaz_responsibility" class="form-select" required>
                        <option value="">Select Teacher</option>
                        <?php $teachers->data_seek(0); while ($teacher = $teachers->fetch_assoc()): ?>
                            <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Daily Visits:</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label>After Fajar:</label>
                            <select name="visit_fajar" class="form-select" required>
                                <option value="">Select Committee Member</option>
                                <?php while ($admin = $admins->fetch_assoc()): ?>
                                    <option value="<?php echo $admin['id']; ?>"><?php echo $admin['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>After Asar:</label>
                            <select name="visit_asar" class="form-select" required>
                                <option value="">Select Committee Member</option>
                                <?php $admins->data_seek(0); while ($admin = $admins->fetch_assoc()): ?>
                                    <option value="<?php echo $admin['id']; ?>"><?php echo $admin['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>After Magrib:</label>
                            <select name="visit_magrib" class="form-select" required>
                                <option value="">Select Committee Member</option>
                                <?php $admins->data_seek(0); while ($admin = $admins->fetch_assoc()): ?>
                                    <option value="<?php echo $admin['id']; ?>"><?php echo $admin['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fees Collection of Committee:</label>
                    <div id="feesCollectionContainer">
                        <div class="input-group mb-2 fees-collection-entry">
                            <select name="fees_admin[]" class="form-select" required>
                                <option value="">Select Committee Member</option>
                                <?php $admins->data_seek(0); while ($admin = $admins->fetch_assoc()): ?>
                                    <option value="<?php echo $admin['id']; ?>"><?php echo $admin['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <input type="number" name="fees_amount[]" class="form-control" placeholder="Enter Amount" required>
                            <button type="button" class="btn btn-success add-fees"><b>+</b></button>
                        </div>
                    </div>
                </div>


                <div class="mb-3">
                    <label class="form-label">Maktab Lock:</label>
                    <select name="maktab_lock" class="form-select" required>
                        <option value="">Select Teacher</option>
                        <?php $teachers->data_seek(0); while ($teacher = $teachers->fetch_assoc()): ?>
                            <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Safai aur akhlak:</label>
                    <select name="cleanliness_ethics" class="form-select" required>
                        <option value="">Select Teacher</option>
                        <?php $teachers->data_seek(0); while ($teacher = $teachers->fetch_assoc()): ?>
                            <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Food Responsibility:</label>
                    <select name="food_responsibility" class="form-select" required>
                        <option value="">Select Committee Member</option>
                        <?php $admins->data_seek(0); while ($admin = $admins->fetch_assoc()): ?>
                            <option value="<?php echo $admin['id']; ?>"><?php echo $admin['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Add Meeting</button>
            </form>
        </div>
    </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".add-fees").click(function () {
            let newEntry = `
                <div class="input-group mb-2 fees-collection-entry">
                    <select name="fees_admin[]" class="form-select" required>
                        <option value="">Select Admin</option>
                        <?php $admins->data_seek(0); while ($admin = $admins->fetch_assoc()): ?>
                            <option value="<?php echo $admin['id']; ?>"><?php echo $admin['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="number" name="fees_amount[]" class="form-control" placeholder="Enter Amount" required>
                    <button type="button" class="btn btn-danger remove-fees">❌</button>
                </div>
            `;
            $("#feesCollectionContainer").append(newEntry);
        });

        $(document).on("click", ".remove-fees", function () {
            $(this).closest(".fees-collection-entry").remove();
        });
    });
</script>

