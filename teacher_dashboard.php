<?php
session_start();

// Check for a message and clear it after displaying
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    echo "<p>$message</p>"; // Display the message
}


if (!isset($_SESSION['teacher_id'])) {
    header("Location: login_teacher.php");
    exit();
}

include 'db.php'; // Database connection
$message = ''; // To store messages to display after redirects

// Handle Add Test
if (isset($_POST['add_test'])) {
    $testname = $_POST['testname'];
    $batch = $_POST['batch'];
    $date = $_POST['date'];
    $teacher_id = $_SESSION['teacher_id'];

    if (!empty($testname) && !empty($batch) && !empty($date)) {
        $stmt = $conn->prepare("INSERT INTO Tests (testname, batch, createdby, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $testname, $batch, $teacher_id, $date);
        if ($stmt->execute()) {
            echo "<p>New test added successfully!</p>";
        } else {
            echo "<p>Error adding test: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Please fill in all fields.</p>";
    }
    // Redirect to prevent form resubmission
    $_SESSION['message'] = 'New test added successfully!'; // Set the success message
    header('Location: teacher_dashboard.php');
    exit();
}

// Handle Delete Test
if (isset($_GET['delete_test'])) {
    $test_id = $_GET['delete_test'];
    $stmt = $conn->prepare("DELETE FROM Tests WHERE id = ? AND createdby = ?");
    $stmt->bind_param("ii", $test_id, $_SESSION['teacher_id']);
    if ($stmt->execute()) {
        echo "<p>Test deleted successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();

     // Redirect to prevent form resubmission
     $_SESSION['message'] = 'Test deleted successfully!';
     header('Location: teacher_dashboard.php');
     exit();
}





// Handle Add Score
if (isset($_POST['add_score'])) {
    $rollno = $_POST['rollno'];
    $batch = $_POST['score_batch'];  // Ensure this matches the input field name
    $testname = $_POST['score_testname'];
    $testid = $_POST['score_testid'];
    $totalmarks = $_POST['totalmarks'];
    $rightquestion = $_POST['rightquestion'];
    $wrongquestion = $_POST['wrongquestion'];
    $notattempted = $_POST['notattempted'];

    // Calculate percentage dynamically
    $percentage = ($rightquestion / $totalmarks) * 100;

    $stmt = $conn->prepare("INSERT INTO TestScores (rollno, batch, testname, testid, totalmarks, rightquestion, wrongquestion, notattempted, percentage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isisiiidi", $rollno, $batch, $testname, $testid, $totalmarks, $rightquestion, $wrongquestion, $notattempted, $percentage);
    if ($stmt->execute()) {
        echo "<p>Score added successfully!</p>";
    } else {
        echo "<p>Error adding score: " . $stmt->error . "</p>";
    }
    
    // Redirect to prevent form resubmission
    $_SESSION['message'] = 'Score added successfully!';
    header('Location: teacher_dashboard.php');
    exit();
    $stmt->close();
}

// Handle Delete Score
if (isset($_GET['delete_score'])) {
    $rollno = $_GET['rollno'];
    $testid = $_GET['testid'];

    $stmt = $conn->prepare("DELETE FROM TestScores WHERE rollno = ? AND testid = ?");
    $stmt->bind_param("ii", $rollno, $testid);
    if ($stmt->execute()) {
        echo "<p>Score deleted successfully!</p>";
    } else {
        echo "<p>Error deleting score: " . $stmt->error . "</p>";
    }
    $stmt->close();
    // Redirect to prevent form resubmission
    $_SESSION['message'] = 'Score deleted successfully!';
    header('Location: teacher_dashboard.php');
    exit();
}


// Fetch Scores by Roll Number
$scores = [];
if (isset($_POST['view_scores'])) {
    $rollno = $_POST['rollno_view'];
    $stmt = $conn->prepare("SELECT * FROM TestScores WHERE rollno = ?");
    $stmt->bind_param("i", $rollno);
    $stmt->execute();
    $result = $stmt->get_result();
    $scores = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch all tests created by the logged-in teacher
$stmt = $conn->prepare("SELECT * FROM Tests WHERE createdby = ?");
$stmt->bind_param("i", $_SESSION['teacher_id']);
$stmt->execute();
$tests_result = $stmt->get_result();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="./css/teacher_dashboard.css">
</head>

<body>
    <h1>Welcome, <?php echo $_SESSION['teacher_name']; ?></h1>

<!-- Logout Button -->
<form action="logout.php" method="post">
    <input type="submit" value="Logout">
</form>
    <h2>Create New Test</h2>
    <form action="teacher_dashboard.php" method="post">
        Test Name: <input type="text" name="testname" required><br>
        Batch: <input type="text" name="batch" required><br>
        Date: <input type="date" name="date" required><br>
        <input type="submit" name="add_test" value="Add Test">
    </form>

    <h2>Add Scores for a Test</h2>
<form action="teacher_dashboard.php" method="post">
    Roll No: <input type="text" name="rollno" required><br>
    Batch: <input type="text" name="score_batch" required><br>
    Test Name (for reference): <input type="text" name="score_testname" required><br>
    Test ID: <input type="text" name="score_testid" required><br>
    Total Marks: <input type="number" name="totalmarks" required><br>
    Right Questions: <input type="number" name="rightquestion" required><br>
    Wrong Questions: <input type="number" name="wrongquestion" required><br>
    Not Attempted: <input type="number" name="notattempted" required><br>
    <input type="submit" name="add_score" value="Add Score">
</form>



    <h2>View Scores by Roll Number</h2>
    <form action="teacher_dashboard.php" method="post">
        Roll No: <input type="text" name="rollno_view" required><br>
        <input type="submit" name="view_scores" value="View Scores">
    </form>

    <h3>Scores for Roll No: <?php echo htmlspecialchars($rollno); ?></h3>
<table border="1">
    <tr>
        <th>Test ID</th>
        <th>Batch</th>
        <th>Total Marks</th>
        <th>Right Questions</th>
        <th>Wrong Questions</th>
        <th>Not Attempted</th>
        <th>Percentage</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($scores as $score): ?>
    <tr>
        <td><?php echo htmlspecialchars($score['testid']); ?></td>
        <td><?php echo htmlspecialchars($score['batch']); ?></td>
        <td><?php echo htmlspecialchars($score['totalmarks']); ?></td>
        <td><?php echo htmlspecialchars($score['rightquestion']); ?></td>
        <td><?php echo htmlspecialchars($score['wrongquestion']); ?></td>
        <td><?php echo htmlspecialchars($score['notattempted']); ?></td>
        <td><?php echo htmlspecialchars($score['percentage']); ?>%</td>
        <td>
            <a href="teacher_dashboard.php?delete_score=1&rollno=<?php echo $score['rollno']; ?>&testid=<?php echo $score['testid']; ?>" onclick="return confirm('Are you sure you want to delete this score?');">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>


    
   

    <h2>Your Tests</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Test Name</th>
            <th>Batch</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($test = $tests_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($test['id']); ?></td>
                <td><?php echo htmlspecialchars($test['testname']); ?></td>
                <td><?php echo htmlspecialchars($test['batch']); ?></td>
                <td><?php echo htmlspecialchars($test['date']); ?></td>
                <td>
                    <a href="view_test_scores.php?test_id=<?php echo $test['id']; ?>">View Scores</a> |
                    <a href="teacher_dashboard.php?delete_test=<?php echo $test['id']; ?>" onclick="return confirm('Are you sure you want to delete this test?');">Delete Test</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>


<?php
$conn->close();
?>
