<?php
session_start();

// Check if student is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.html"); // Redirect to index.html if not logged in
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

// Fetch student details using the logged-in email
$student_email = $_SESSION['email'];

// Fetch student name
$student_query = "SELECT name FROM students WHERE email='$student_email'";
$student_result = $conn->query($student_query);

if ($student_result->num_rows > 0) {
    // Student found, fetch details
    $student_row = $student_result->fetch_assoc();
    $student_name = $student_row['name'];
} else {
    // Student not found, handle error or set default value
    $student_name = "N/A";
}

// Fetch requests of the particular student where payment_status is 'paid'
$request_query = "SELECT * FROM student_tutor_request WHERE student_email='$student_email' AND payment_status='paid'";
$request_result = $conn->query($request_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
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
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="student_home.php">Home</a>
    <a href="student_view_courses.php"><b><i>My Courses</i></b></a>
    <a href="student_buy_course.php">Request Tutor</a>
    <a href="student_view_status.php">View Request Status</a>
    <a href="student_update_profile.php">Update Profile</a>
    <!-- Add more options here -->
</div>

<div class="main">
    <h2>My Courses</h2>
    <!-- Display student name -->
    <h3>Hello <?php echo $student_name; ?></h3>
    <div class="content">
        <!-- Display table of paid courses -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Tutor Name</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($request_result->num_rows > 0) {
                    // Output data of each row
                    while($row = $request_result->fetch_assoc()) {
                        // Fetch course name using course ID
                        $course_id = $row["course_id"];
                        $course_query = "SELECT course_name FROM courses WHERE id='$course_id'";
                        $course_result = $conn->query($course_query);
                        $course_name = "";
                        if ($course_result->num_rows > 0) {
                            $course_row = $course_result->fetch_assoc();
                            $course_name = $course_row["course_name"];
                        }
                        echo "<tr>";
                        echo "<td>".$row["course_id"]."</td>";
                        echo "<td>".$course_name."</td>"; // Display course name
                        echo "<td>".$row["tutor_name"]."</td>";
                        echo "<td>".$row["payment_status"]."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No courses found.</td></tr>";
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
