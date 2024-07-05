<?php
session_start();

// Check if student is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.html"); // Redirect to index.html if not logged in
    exit;
}

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

// Function to fetch student name from students table
function getStudentName($student_email, $conn) {
    $student_query = "SELECT name FROM students WHERE email='$student_email'";
    $student_result = $conn->query($student_query);
    if ($student_result->num_rows > 0) {
        $student_row = $student_result->fetch_assoc();
        return $student_row['name'];
    } else {
        return "N/A";
    }
}

// Function to fetch course name from courses table
function getCourseName($course_id, $conn) {
    $course_query = "SELECT course_name FROM courses WHERE id='$course_id'";
    $course_result = $conn->query($course_query);
    if ($course_result->num_rows > 0) {
        $course_row = $course_result->fetch_assoc();
        return $course_row['course_name'];
    } else {
        return "N/A";
    }
}

// Fetch student details using the logged-in email
$student_email = $_SESSION['email'];

// Fetch requests for the logged-in student
$request_query = "SELECT * FROM student_tutor_request WHERE student_email='$student_email'";
$request_result = $conn->query($request_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home</title>
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
        .content {
            text-align: center;
            border: 5px solid black; /* Border with a width of 2px and black color */
            padding: 20px; /* Padding inside the box */
            margin: 20px; /* Margin around the box */
            width: 1100px;
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="student_home.php">Home</a>
    <a href="student_view_courses.php">My Courses</a>
    <a href="student_buy_course.php">Request Tutor</a>
    <a href="student_view_status.php"><b><i>View Request Status</i></b></a>
    <a href="student_update_profile.php">Update Profile</a>
    <!-- Add more options here -->
</div>
<h2>View Request Status</h2>
<div class="content">

<div class="container">
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Name</th>
                <th>Tutor Name</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($request_result->num_rows > 0) {
                while ($row = $request_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['course_id'] . "</td>";
                    echo "<td>" . getCourseName($row['course_id'], $conn) . "</td>";
                    echo "<td>" . $row['tutor_name'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>" . $row['payment_status'] . "</td>";
                    echo "<td>";
                    if ($row['status'] == 'Accepted') {
                        if ($row['payment_status'] == 'Pending') {
                            echo "<a href='student_payment_page.php?request_id=" . $row['id'] . "' class='btn btn-primary'>Proceed to Payment</a>";
                        } elseif ($row['payment_status'] == 'Paid') {
                            $student_name = getStudentName($row['student_email'], $conn);
                            echo "<a href='student_course_page.php?course_id=" . $row['course_id'] . "&course_name=" . urlencode(getCourseName($row['course_id'], $conn)) . "&student_name=" . urlencode($student_name) . "&student_email=" . $row['student_email'] . "&tutor_name=" . urlencode($row['tutor_name']) . "' class='btn btn-success'>Go to Course Page</a>";
                        }
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No requests found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>
