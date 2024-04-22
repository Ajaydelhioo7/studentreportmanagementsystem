<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
    <link rel="stylesheet" href="./css/register_teacher.css">
</head>
<body>
    <h1>Register Teacher</h1>
    <div class="container">
    <form action="register_teacher.php" method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Email: <input type="email" name="email" required><br>
        Phone: <input type="text" name="phone" required><br>
        <input type="submit" name="submit" value="Register">
    </form>

    </div>
    
    <?php
    // Include the database connection file
    include 'db.php';

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // retrieve the form data by using the element's name attributes value as key
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash the password for security
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        // prepare and bind
        $stmt = $conn->prepare("INSERT INTO Teachers (username, password, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $email, $phone);

        // execute and check errors
        if ($stmt->execute()) {
            echo "New teacher registered successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
