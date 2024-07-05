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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch list of courses from the courses table
$course_query = "SELECT course_name, course_description, course_subject, course_price, course_tutors FROM courses";
$course_result = $conn->query($course_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Course List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
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

        .main {
            margin-left: 250px;
            padding: 20px;
        }

        .hidden {
            display: none;
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="admin_home.php"><i><b>Home</i></b></a>
    <a href="admin_add_course.php">Add Course</a>
    <a href="admin_delete_course.php">Delete Course</a>
    <a href="admin_tutors_list.php">Tutors List</a>
    <a href="admin_course_list.php"><b><i>Courses List</i></b></a>
    <a href="admin_view_student_feedback.php">Student Feedback</a>
    <a href="admin_tutor_course_requests.php">Tutor Course Requests</a>
    <!-- Add more actions here -->
</div>

<div class="main">
    <h2>Courses List</h2>
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Description</th>
                    <th>Course Subject</th>
                    <th>Course Tutors</th>
                    <th>Course Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($course_result->num_rows > 0) {
                    while ($row = $course_result->fetch_assoc()) {
                        // Split the course_tutors string using comma as delimiter
                        $tutors = explode(",", $row["course_tutors"]);
                        $firstTutor = true;
                        // Iterate over each tutor and display on separate lines
                        foreach ($tutors as $tutor) {
                            echo "<tr>";
                            if ($firstTutor) {
                                echo "<td rowspan='" . count($tutors) . "'>" . $row["course_name"] . "</td>";
                                echo "<td rowspan='" . count($tutors) . "'>" . $row["course_description"] . "</td>";
                                echo "<td rowspan='" . count($tutors) . "'>" . $row["course_subject"] . "</td>";
                                echo "<td>" . $tutor . "</td>";
                                echo "<td rowspan='" . count($tutors) . "'>" . $row["course_price"] . "</td>";
                                $firstTutor = false;
                            } else {
                                echo "<td>" . $tutor . "</td>";
                            }
                            echo "</tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='5'>No courses found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</center>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
