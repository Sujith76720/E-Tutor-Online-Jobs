<?php
session_start();

// Check if tutor is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.html"); // Redirect to index.html if not logged in
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
$database = "software"; // Replace "your_database" with the actual name of your database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tutor details using the logged-in email
$tutor_email = $_SESSION['email'];
$tutor_query = "SELECT * FROM tutors WHERE email='$tutor_email'";
$tutor_result = $conn->query($tutor_query);

if ($tutor_result->num_rows > 0) {
    // Tutor found, fetch details
    $tutor_row = $tutor_result->fetch_assoc();
    $tutor_name = $tutor_row['name'];
} else {
    // Tutor not found, redirect to login page
    header("Location: login_process.php");
    exit;
}

// Fetch courses handled by the tutor
$courses_query = "SELECT * FROM courses WHERE course_tutors LIKE '%$tutor_name%'";
$courses_result = $conn->query($courses_query);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Your Courses</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to left, #e0ac69, #fff8e1);
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            color: #f1f1f1;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
        }

        .student-box {
            width: 500px;
            background: linear-gradient(to right, #800080, #ff69b4);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        .student-box:hover {
            transform: scale(1.01); /* Increase size by 10% on hover */
        }

        .student-count {
            font-size: 24px;
            color: black; /* Deep Sea Blue */
        }
        
        .container {
            text-align: center;
            border: 2px solid black; /* Border with a width of 2px and black color */
            padding: 20px; /* Padding inside the box */
            margin: 20px; /* Margin around the box */
            width: 1000px;
        }

        .table thead th {
            background-color: #343a40; /* Dark Gray */
            color: white;
        }

        .table tbody tr {
            border-bottom: 5px solid #343a40; /* Dark Gray */
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        /* CSS for the table */
        table {
            width: 100%;
            border-collapse: collapse; /* Collapse the borders into a single border */
        }

        /* CSS for table headings */
        th {
            border: 5px solid #343a40; /* Dark Gray */
            padding: 8px; /* Add padding to the table headings */
        }

        /* CSS for table cells */
        td {
            border: 5px solid #343a40; /* Dark Gray */
            padding: 8px; /* Add padding to the table cells */
        }

    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="tutor_home.php">Home</a>
    <a href="tutor_view_courses.php"><b><i>View Your Courses</i></b></a>
    <a href="tutor_take_course.php">Take a Course</a>
    <a href="tutor_delete_course.php">Remove a Course</a>
    <a href="tutor_view_requests.php">View Student Requests</a>
    <a href="tutor_update_profile.php">Update Profile</a>
    <a href="tutor_course_request.php">Request a Course</a>
    <!-- Add more actions here -->
</div>

    <div class="container">
        <h2>Your Courses</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Fee</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($courses_result->num_rows > 0) {
                    // Output data of each row
                    while($row = $courses_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row["course_name"]."</td>";
                        echo "<td>".$row["course_description"]."</td>";
                        echo "<td>₹".$row["course_price"]."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No courses available.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Button to return to tutor_home.php -->
        <!-- <a href="tutor_home.php" class="btn btn-secondary">Return to Home</a>
        <a href="tutor_take_course.php" class="btn btn-primary">Take a Course</a>
        <a href="tutor_delete_course.php" class="btn btn-primary">Remove a Course</a>
        <a href="tutor_update_profile.php" class="btn btn-primary">Update Profile</a> -->
    </div>
</center>
</body>
</html>
