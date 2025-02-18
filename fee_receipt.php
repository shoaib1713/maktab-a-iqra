<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - Maktab-a-Ekra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
                    <h2>Fee Receipt</h2>
                </div>
            </nav>
            <div class="container mt-4">
                <form method="POST" id="receiptForm">
                    <label for="academic_year" class="form-label">Select Academic Year:</label>
                    <select name="academic_year" id="academic_year" class="form-select">
                        <option value=''>Select Year</option>
                        <?php
                        $currentYear = date("Y");
                        $startYear = $currentYear - 5;
                        $endYear = $currentYear + 1;
                        for ($year = $startYear; $year <= $endYear; $year++) {
                            $academicYear = $year . "-" . ($year + 1);
                            echo "<option value='$academicYear'>$academicYear (June - May)</option>";
                        }
                        ?>
                    </select>
                </form>
                <div id="receiptContent" class="mt-4 p-3 border d-none">
                    <h4>Receipt Details</h4>
                    <p><strong>Academic Year:</strong> <span id="yearText"></span></p>
                    <p><strong>Student Name:</strong> John Doe</p>
                    <p><strong>Amount Paid:</strong> ₹10,000</p>
                    <p><strong>Date:</strong> <?php echo date("d-m-Y"); ?></p>
                    <button class="btn btn-primary" onclick="printReceipt()">Print</button>
                    <button class="btn btn-success" onclick="sendReceipt()">Send via Email/SMS</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#academic_year').change(function() {
                var year = $(this).val();
                if (year) {
                    $('#yearText').text(year);
                    $('#receiptContent').removeClass('d-none');
                } else {
                    $('#receiptContent').addClass('d-none');
                }
            });
        });

        function printReceipt() {
            window.print();
        }
        
        function sendReceipt() {
            alert("Receipt sent via Email/SMS successfully!");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
