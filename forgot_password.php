<!-- Forgot Password Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Maktab-a-Ekra</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form id="forgotPasswordForm" action="modules/forgot_password.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" required>
            
            <button type="submit">Reset Password</button>
            <p id="forgot-msg" class="error"></p>
        </form>
        <div class="links">
            <a href="index.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
