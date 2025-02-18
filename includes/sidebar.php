<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'];
?>

<div class="bg-dark text-white" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 fs-4"> <img src="assets/images/logo.png" alt="Student Photo" class="img-thumbnail" width="100"></div>
    <div class="list-group list-group-flush">
        <a href="<?php echo ($role == 'teacher') ? 'teacher_dashboard.php': 'dashboard.php' ?>" class="list-group-item list-group-item-action bg-dark text-white">Home</a>
        <a href="students.php" class="list-group-item list-group-item-action bg-dark text-white">Total Students</a>
        <a href="fees_collection.php" class="list-group-item list-group-item-action bg-dark text-white">Fees Collection</a>
        <a href="pending_fees.php" class="list-group-item list-group-item-action bg-dark text-white">Student Fees Details</a>
        <a href="meeting_list.php" class="list-group-item list-group-item-action bg-dark text-white">Meeting Details</a>
        <a href="maintenance_list.php" class="list-group-item list-group-item-action bg-dark text-white">Maintenance Details</a>
        <?php if($role=='admin') { ?>
        <a href="cheque_details.php" class="list-group-item list-group-item-action bg-dark text-white">Cheque Details</a>
        <?php } ?>
        <?php if($role=='admin') { ?>
        <a href="approve_fee.php" class="list-group-item list-group-item-action bg-dark text-white">Fees Approval</a>
        <?php } ?>
        <a href="users.php" class="list-group-item list-group-item-action bg-dark text-white">Users</a>
    </div>
</div>
