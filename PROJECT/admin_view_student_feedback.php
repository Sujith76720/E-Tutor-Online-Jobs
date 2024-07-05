<?php
// Start or resume session
session_start();

// Check if the user is not logged in, then redirect to index.html
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy session data
    header("Location: index.html"); // Redirect to index.html after logout
    exit;
}

// Establish a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = ""; // If you have set a password, provide it here
$database = "software"; // Replace "your_database_name" with the actual name of your database
$conn = new mysqli($servername, $username, $password, $database);

// Fetch feedback data
$feedback_query = "SELECT * FROM course_details WHERE student_feedback IS NOT NULL";
$feedback_result = $conn->query($feedback_query);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Student Feedback</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-left: 250px;
            padding: 20px;
        }
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            background-color: #111;
            padding-top: 100px;
        }
        .sidebar a {
            padding: 20px 15px;
            font-size: 25px;
            color: #818181;
            display: block;
        }
        .sidebar a:hover {
            color: #f1f1f1;
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
    <center>
<div class="sidebar">
    <a href="admin_home.php">Home</a>
    <a href="admin_add_course.php">Add Course</a>
    <a href="admin_delete_course.php">Delete Course</a>
    <a href="admin_tutors_list.php">Tutors List</a>
    <a href="admin_course_list.php">Courses List</a>
    <a href="admin_view_student_feedback.php"><i><b>Student Feedback</i></b></a>
    <a href="admin_tutor_course_requests.php">Tutor Course Requests</a>
    <!-- Add more actions here -->
</div>

<div class="container">
    <h2>Student Feedback</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student Email</th>
                <th>Tutor Name</th>
                <th>Tutor Email</th>
                <th>Course ID</th>
                <th>Course Name</th>
                <th>Class Datetime</th>
                <th>Student Feedback</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($feedback_result->num_rows > 0): ?>
                <?php while($row = $feedback_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?php echo $row['student_email']; ?></td>
                        <td><?php echo $row['tutor_name']; ?></td>
                        <td><?php echo $row['tutor_email']; ?></td>
                        <td><?php echo $row['course_id']; ?></td>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['class_datetime']; ?></td>
                        <td><?php echo $row['student_feedback']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No feedback available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
            </center>
</body>
</html>
