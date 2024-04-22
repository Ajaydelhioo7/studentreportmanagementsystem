<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    // If the session is not set, redirect to login page
    header("Location: login_student.php");
    exit();
}

echo "Welcome " . $_SESSION['student_name'] . "!"; // Welcoming the user
?>
<!-- HTML and PHP to display student dashboard content -->
