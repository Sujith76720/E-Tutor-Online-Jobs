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
    $tutor_subject = $tutor_row['subject']; // Ensure $tutor_subject is set
    $tutor_name = $tutor_row['name'];
} else {
    // Tutor not found, redirect to login page
    header("Location: login_process.php");
    exit;
}

// Fetch courses with similar subjects to the tutor's subject
if ($tutor_subject !== null) {
    $course_query = "SELECT * FROM courses WHERE course_subject LIKE '%$tutor_subject%'";
    $course_result = $conn->query($course_query);
} else {
    $course_result = false;
    $enroll_message = "You have not selected the subject. First update the subject in the Update Profile Section.";
}

// Initialize message variable
$take_course_message = "";

// Check if the course has been taken
if (isset($_GET['course_name'])) {
    $course_name = $_GET['course_name'];
    // Check if the tutor has already taken the course
    $check_query = "SELECT * FROM courses WHERE course_name = '$course_name' AND course_tutors LIKE '%$tutor_name%'";
    $check_result = $conn->query($check_query);
    if ($check_result && $check_result->num_rows > 0) {
        $take_course_message = "<span style='color: red;'>Course already taken</span>";
    } else {
        // Update the course_tutors column in the courses table
        $update_query = "UPDATE courses SET course_tutors = CONCAT_WS(', ', course_tutors, '$tutor_name') WHERE course_name = '$course_name'";
        if ($conn->query($update_query) === TRUE) {
            $take_course_message = "<span style='color: green;'>Course taken successfully.</span>";
        } else {
            $take_course_message = "<span style='color: red;'>Error taking course: " . $conn->error . "</span>";
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take a Course</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
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
</head>
<body>
<center>
<div class="sidebar">
    <a href="tutor_home.php">Home</a>
    <a href="tutor_view_courses.php">View Your Courses</a>
    <a href="tutor_take_course.php"><b><i>Take a Course</i></b></a>
    <a href="tutor_delete_course.php">Remove a Course</a>
    <a href="tutor_view_requests.php">View Student Requests</a>
    <a href="tutor_update_profile.php">Update Profile</a>
    <a href="tutor_course_request.php">Request a Course</a>
    <!-- Add more actions here -->
</div>
<!-- Your HTML code -->
<center>
    <div class="container">
        <h2>Available Courses</h2>
        <p>Here are the courses available based on your subject: <?php echo isset($tutor_subject) ? $tutor_subject : "No subject found"; ?></p>

        <?php if(isset($enroll_message)) echo "<p>$enroll_message</p>"; ?>
        <?php if(!empty($take_course_message)) echo "<p>$take_course_message</p>"; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Fee</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($course_result !== false && $course_result->num_rows > 0) {
                    // Output data of each row
                    while($row = $course_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row["course_name"]."</td>";
                        echo "<td>".$row["course_description"]."</td>";
                        echo "<td>₹".$row["course_price"]."</td>";
                        if (strpos($row["course_tutors"], $tutor_name) !== false) {
                            echo "<td>Course already taken</td>";
                        } else {
                            echo "<td><a href='?course_name=".$row["course_name"]."'>Take Course</a></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No courses available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</center>
</body>
</html>
