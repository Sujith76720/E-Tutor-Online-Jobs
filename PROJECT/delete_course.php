<?php
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

// Extract course name to delete from the POST request
$courseNameToDelete = $_POST['courseNameToDelete'];

// Prepare SQL query to delete the course
$delete_query = "DELETE FROM courses WHERE course_name='$courseNameToDelete'";

// Execute the delete query
if ($conn->query($delete_query) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo "Course deleted successfully.";
    } else {
        echo "Error: Course not found in the database.";
    }
} else {
    echo "Error deleting the course: " . $conn->error;
}

// Close the connection
$conn->close();
?>
