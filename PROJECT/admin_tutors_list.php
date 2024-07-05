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

// Fetch list of tutors from the tutors table
$tutor_query = "SELECT * FROM tutors";
$tutor_result = $conn->query($tutor_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Tutors List</title>
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
    <a href="admin_tutors_list.php"><b><i>Tutors List</i></b></a>
    <a href="admin_course_list.php">Courses List</a>
    <a href="admin_view_student_feedback.php">Student Feedback</a>
    <a href="admin_tutor_course_requests.php">Tutor Course Requests</a>
    <!-- Add more actions here -->
</div>

<div class="main">
    <h2>Tutors List</h2>
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($tutor_result->num_rows > 0) {
                    while ($row = $tutor_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row["name"]."</td>";
                        echo "<td>".$row["email"]."</td>";
                        echo "<td>".$row["subject"]."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No tutors found.</td></tr>";
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
