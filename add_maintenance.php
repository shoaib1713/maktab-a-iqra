<?php 
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            require_once 'config.php';
            require 'config/db.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $months = $_POST['month'];
                $years = $_POST['year'];
                $categories = $_POST['category'];
                $amounts = $_POST['amount'];
                $comments = $_POST['comment'] ?? [];
                $created_by = $_SESSION['user_id']; // Change this as per your user authentication system
                $created_on = date("Y-m-d H:i:s");
            
                $stmt = $conn->prepare("INSERT INTO maintenance (created_by, created_on, month, year, category, amount, comment) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ississs", $created_by, $created_on, $month, $year, $category, $amount, $comment);
            
                for ($i = 0; $i < count($months); $i++) {
                    $month = $months[$i];
                    $year = $years[$i];
                    $category = $categories[$i];
                    $amount = $amounts[$i];
                    $comment = ($category == "Miscellaneous") ? ($comments[$i] ?? '') : '';
            
                    $stmt->execute();
                }
            
                $stmt->close();
                $conn->close();
            
                header("Location: maintenance_list.php"); // Redirect after saving
                exit();
            } else {
                echo "Invalid request!";
            }
            
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a href='maintenance_list.php' class="btn btn-primary">Home</a>
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤 <?php echo $_SESSION['user_name']; ?></span>
                        <a href="modules/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </nav>
            <h2 class="mb-4">Add Maintenance</h2>

            <form method="POST">
                <div id="maintenance_entries">
                    <div class="entry row mb-3">
                        <div class="col-md-3">
                            <label>Month:</label>
                            <select name="month[]" class="form-control">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>"><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Year:</label>
                            <select name="year[]" class="form-control">
                                <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Category:</label>
                            <select name="category[]" class="form-control category-select">
                                <option value="Maktab Rest">Maktab Rent</option>
                                <option value="Maktab Safai">Maktab Safai</option>
                                <option value="Water">Water</option>
                                <option value="Miscellaneous">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Amount (₹):</label>
                            <input type="number" name="amount[]" class="form-control" required>
                        </div>
                        <div class="col-md-12 mt-2 misc-comment" style="display: none;">
                            <label>Comment:</label>
                            <textarea name="comment[]" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-2 mt-2">
                            <button type="button" class="btn btn-danger remove-entry">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add_more" class="btn btn-secondary">Add More</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</body>

</html>

<script>
document.getElementById('add_more').addEventListener('click', function() {
    let newEntry = document.querySelector('.entry').cloneNode(true);
    newEntry.querySelectorAll('input, select, textarea').forEach(input => input.value = '');
    newEntry.querySelector('.misc-comment').style.display = 'none';
    document.getElementById('maintenance_entries').appendChild(newEntry);
});

document.addEventListener('change', function(event) {
    if (event.target.classList.contains('category-select')) {
        let entry = event.target.closest('.entry');
        let commentBox = entry.querySelector('.misc-comment');
        if (event.target.value === 'Miscellaneous') {
            commentBox.style.display = 'block';
        } else {
            commentBox.style.display = 'none';
        }
    }
});

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-entry')) {
        let entries = document.querySelectorAll('.entry');
        if (entries.length > 1) {
            event.target.closest('.entry').remove();
        }
    }
});
</script>