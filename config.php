<?php
// Database Connection Constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'maktab_a_ekra');

define('STUDENT_MONTHLY_FEES',1800);
define('ACEDEMIC_START_MONTH',6);
define('ACEDEMIC_END_MONTH',5);

// Base URL (Modify it according to your project folder structure)
define('BASE_URL', 'http://localhost/maktab-a-ekra/');

// Other Global Constants
define('SITE_NAME', 'Maktab-a-Ekra');
define('ADMIN_EMAIL', 'admin@maktab-a-ekra.com');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
