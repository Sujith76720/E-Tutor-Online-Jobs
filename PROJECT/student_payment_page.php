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

// Check if request_id is provided in the URL
if (!isset($_GET['request_id'])) {
    header("Location: student_view_status.php"); // Redirect to student_view_status.php if request_id is not provided
    exit;
}

$request_id = $_GET['request_id'];

// Fetch request details
$request_query = "SELECT * FROM student_tutor_request WHERE id='$request_id'";
$request_result = $conn->query($request_query);

if ($request_result->num_rows == 0) {
    header("Location: student_view_status.php"); // Redirect to student_view_status.php if request is not found
    exit;
}

$row = $request_result->fetch_assoc();
$course_id = $row['course_id'];
$tutor_name = $row['tutor_name'];
$status = $row['status'];

// Fetch course details based on course ID
$course_query = "SELECT * FROM courses WHERE id='$course_id'";
$course_result = $conn->query($course_query);

if ($course_result->num_rows == 0) {
    header("Location: student_view_status.php"); // Redirect to student_view_status.php if course is not found
    exit;
}

$course_row = $course_result->fetch_assoc();
$course_name = $course_row['course_name'];
$course_description = $course_row['course_description'];
$course_price = $course_row['course_price'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            border: none;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .status {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Payment Page</h2>
    <div class="card">
        <div class="card-header">
            Course Details
        </div>
        <div class="card-body">
            <h5 class="card-title">Course Name: <?php echo $course_name; ?></h5>
            <p class="card-text">Description: <?php echo $course_description; ?></p>
            <p class="card-text">Price: ₹<?php echo $course_price; ?></p>
            <p class="card-text">Tutor Name: <?php echo $tutor_name; ?></p>
        </div>
    </div>
    <div class="status">Status: <?php echo $status; ?></div>
    <a href="student_process_payment.php?request_id=<?php echo $request_id; ?>" class="btn btn-primary btn-block mt-3">Proceed to Payment</a>
</div>
</body>
</html>
