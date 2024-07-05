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
    // Add more details here as needed
} else {
    // Tutor not found, redirect to index.html
    header("Location: index.html");
    exit;
}

// Fetch the number of registered students
$students_query = "SELECT COUNT(*) AS total_students FROM students";
$students_result = $conn->query($students_query);
$student_count = $students_result->fetch_assoc()['total_students'];

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Home</title>
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
            text align: center;
            border: 2px solid black; /* Border with a width of 2px and black color */
            padding: 20px; /* Padding inside the box */
            margin: 20px; /* Margin around the box */
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="tutor_home.php"><b><i>Home</i></b></a>
    <a href="tutor_view_courses.php">View Your Courses</a>
    <a href="tutor_take_course.php">Take a Course</a>
    <a href="tutor_delete_course.php">Remove a Course</a>
    <a href="tutor_view_requests.php">View Student Requests</a>
    <a href="tutor_update_profile.php">Update Profile</a>
    <a href="tutor_course_request.php">Request a Course</a>
    <!-- Add more actions here -->
</div>

<div class="main">
    <h2>Welcome <?php echo $tutor_name; ?></h2>
    <h3>Email: <?php echo $tutor_email; ?></h3>
    <!-- Additional content to generate interest -->
    <div class="content" >
    <center><h3>Welcome to E Tutor Online Jobs – Your Gateway to Learning and Teaching!</h3></center>
    <p>At E Tutor Online Jobs, we bridge the gap between knowledge seekers and knowledge providers. Our platform is designed for students who are eager to learn and tutors who are passionate about teaching.</p><br>
    <p>Join our community of educators and:
        Share your knowledge with a diverse group of learners.
        Expand your teaching horizons with flexible scheduling and diverse subject options.</p>
    <p>As a dedicated educator, you have the power to make a lasting impact. At E Tutor Online Jobs, we value the knowledge and passion you bring to the table.</p>
    <center><h4>Join Our Mission: We’re on a mission to democratize education, and you’re the key.</h4></center>
    <p>By joining E Tutor Online Jobs, you’re not just teaching; you’re inspiring a new generation of learners.
    Our mission at E Tutor Online Jobs is to revolutionize the way knowledge is shared and learned online. We believe that education is the cornerstone of personal development and societal growth, and our platform is committed to making high-quality education accessible to everyone, everywhere.
    </p>
    <p>At E Tutor Online Jobs, we’re not just teaching subjects; we’re nurturing minds and empowering individuals to reach their full potential. Join us on this journey to shape the future of education.</p>
    <!-- Display the number of registered students -->
    <center><div class="student-box">
        <p class="student-count"><?php echo $student_count; ?></p>
        <p>Registered Students</p>
    </div>
    </div>
</div>
</center>
</body>
</html>
