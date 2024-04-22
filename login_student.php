<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
</head>
<body>
    <h1>Student Login</h1>
    <form action="login_student.php" method="post">
        Roll No: <input type="text" name="rollno" required><br>
        Batch: <input type="text" name="batch" required><br>
        <input type="submit" name="submit" value="Login">
    </form>

    <?php
session_start(); // Start the session at the very beginning

include 'db.php'; // Database connection

if (isset($_POST['submit'])) {
    $rollno = $_POST['rollno'];
    $batch = $_POST['batch'];

    $stmt = $conn->prepare("SELECT * FROM Students WHERE rollno = ? AND batch = ?");
    $stmt->bind_param("is", $rollno, $batch);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Login success
        $user = $result->fetch_assoc();
        $_SESSION['student_id'] = $user['id']; // Store user data in session
        $_SESSION['student_name'] = $user['name']; // Store more data if needed
        header("Location: student_dashboard.php"); // Redirect to the student dashboard
        exit();
    } else {
        // Login failed
        echo "Invalid Roll No or Batch!";
    }

    $stmt->close();
    $conn->close();
}
?>
</body>
</html>
