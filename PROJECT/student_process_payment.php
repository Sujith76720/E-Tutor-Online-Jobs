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
$status = $row['status'];

// Check if the status is already paid
if ($status == 'Paid') {
    $_SESSION['payment_message'] = "Payment has already been completed for this request.";
    header("Location: student_view_status.php");
    exit;
}

// Update payment status to 'Paid'
$update_query = "UPDATE student_tutor_request SET payment_status = 'Paid' WHERE id='$request_id'";
if ($conn->query($update_query) === TRUE) {
    $_SESSION['payment_message'] = "Payment successful. Redirecting back to view status page.";
    header("Location: student_view_status.php");
    exit;
} else {
    $_SESSION['payment_message'] = "Payment failed. Please try again later.";
    header("Location: student_view_status.php");
    exit;
}
?>
