<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MAKTAB-E-IQRA</title>
    <link rel="icon" href="assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4" style="width: 350px;">
        <h2 class="text-center mb-4">MAKTAB-E-IQRA</h2>
        <form id="loginForm" action="modules/auth.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div id="error-msg" class="alert alert-danger mt-3 text-center d-none" role="alert"></div>
        </form>
        <div class="text-center mt-3">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>

<script>
    $(document).ready(function() {
        $('#loginForm').submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: 'modules/auth.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response === 'admin') {
                        window.location.href = 'dashboard.php';
                    } else if (response === 'teacher') {
                        window.location.href = 'teacher_dashboard.php';
                    } else {
                        $('#error-msg').text(response).removeClass('d-none').hide().fadeIn(500);
                    }
                }
            });
        });
    });
</script>
