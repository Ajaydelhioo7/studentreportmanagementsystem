<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
</head>
<body>
    <h1>Register Student</h1>
    <form action="register_student.php" method="post">
        Roll No: <input type="text" name="rollno" required><br>
        Name: <input type="text" name="name" required><br>
        Batch: <input type="text" name="batch" required><br>
        Email: <input type="email" name="email" required><br>
        Phone: <input type="text" name="phone" required><br>
        <input type="submit" name="submit" value="Register">
    </form>

    <?php
    // Include the database connection file
    include 'db.php';

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // retrieve the form data by using the element's name attributes value as key
        $rollno = $_POST['rollno'];
        $name = $_POST['name'];
        $batch = $_POST['batch'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        // prepare and bind
        $stmt = $conn->prepare("INSERT INTO Students (rollno, name, batch, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $rollno, $name, $batch, $email, $phone);

        // execute and check errors
        if ($stmt->execute()) {
            echo "New student registered successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
