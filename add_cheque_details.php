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
        // Fetch teachers from the database
$teacherQuery = $conn->query("SELECT id, name FROM users where role='teacher'");
$teachers = $teacherQuery->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cheque Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href="cheque_details.php" class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>

            <h2 class="mb-4">Add Cheque Details</h2>
            <form id="chequeForm" method="POST" enctype="multipart/form-data">
                <div id="chequeEntries">
                    <div class="cheque-entry border p-3 mb-3 bg-white rounded shadow-sm">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Cheque Year:</label>
                                <select name="cheque_year[]" class="form-control">
                                    <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Cheque Month:</label>
                                <select name="cheque_month[]" class="form-control">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo $m; ?>"><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Cheque Given Date:</label>
                                <input type="date" name="cheque_given_date[]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Cheque Number:</label>
                                <input type="text" name="cheque_number[]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Amount:</label>
                                <input type="number" name="cheque_amount[]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Cheque Photo:</label>
                                <input type="file" name="cheque_photo[]" class="form-control" accept="image/*" required>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Given To</label>
                                <select class="form-control" id="cheque_handover_teacher"
                                    name="cheque_handover_teacher[]" required>
                                    <option value="">Select Ulma</option>
                                    <?php foreach($teachers as $teacher){?>
                                    <option value="<?= $teacher['id']; ?>"><?= $teacher['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-danger remove-entry mt-4">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addMore" class="btn btn-secondary mb-3">Add More</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div id="responseMessage" class="mt-3"></div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $("#addMore").click(function() {
            let newEntry = $(".cheque-entry:first").clone();
            newEntry.find("input").val("");
            $("#chequeEntries").append(newEntry);
        });

        $(document).on("click", ".remove-entry", function() {
            if ($(".cheque-entry").length > 1) {
                $(this).closest(".cheque-entry").remove();
            }
        });

        $("#chequeForm").submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            formData.append("action", "add_cheque");

            $.ajax({
                url: "insert.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.trim() === "success") {
                        $("#responseMessage").html(
                            "<div class='alert alert-success'>Cheque details added successfully!</div>"
                            );
                        $("#chequeForm")[0].reset();
                    } else {
                        $("#responseMessage").html("<div class='alert alert-danger'>" +
                            response + "</div>");
                    }
                }
            });
        });

    });
    </script>
</body>

</html>