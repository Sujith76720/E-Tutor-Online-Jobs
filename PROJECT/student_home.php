<?php
session_start();

// Check if student is logged in
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

// Fetch student details using the logged-in email
$student_email = $_SESSION['email'];
$student_query = "SELECT * FROM students WHERE email='$student_email'";
$student_result = $conn->query($student_query);

if ($student_result->num_rows > 0) {
    // Student found, fetch details
    $student_row = $student_result->fetch_assoc();
    $student_name = $student_row['name'];
    // Add more details here as needed
} else {
    // Student not found, redirect to index.html
    header("Location: index.html");
    exit;
}

// Close the connection
$conn->close();
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
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="student_home.php"><b><i>Home</i></b></a>
    <a href="student_view_courses.php">My Courses</a>
    <a href="student_buy_course.php">Request Tutor</a>
    <a href="student_view_status.php">View Request Status</a>
    <a href="student_update_profile.php">Update Profile</a>
    <!-- Add more options here -->
</div>

<div class="main">
    <h2>Hello <?php echo $student_name; ?></h2>
    <!-- Additional content to generate interest -->
    <div class="content">
    <center><h3>Welcome to E Tutor Online Jobs – Your Gateway to Learning</h3></center>
    <center><h4>Unlock Learning, Unlock Potential!</h4></center>
    <p>At E Tutor Online Jobs, we believe that quality education should be accessible and affordable for everyone. That’s why we’ve created a unique platform where students can learn the concepts they wish to master without the financial strain.</p><br>
    <p>Join our community of learners and</p>
    <p>Share your knowledge with a diverse group of learners.</p>
    <p>Expand your teaching horizons with flexible scheduling and diverse subject options.</p>
    <center><h4>Join Our Mission: We’re on a mission to democratize education, and you’re the key.</h4></center>
    <p>By joining E Tutor Online Jobs, you’re not just learning subjects; you’re gaining knowledge and skills that will last a lifetime.</p>
    <h5>Why Choose Us?</h5>
    <p><b>Focused Learning</b>: Instead of lengthy courses, we offer bite-sized lessons tailored to specific concepts.</p>
    <p><b>Affordable Prices</b>: Learn what you need at prices that won’t break the bank.</p>
    <p><b>Expert Tutors</b>: Our tutors are passionate experts who love to teach and share knowledge.</p>
    </div>
</div>
</center>
</body>
</html>
