<?php
// Establish a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = ""; // If you have set a password, provide it here
$database = "software"; // Replace "your_database_name" with the actual name of your database
$conn = new mysqli($servername, $username, $password, $database);

// Check if the action is to add a course
if ($_POST['action'] == "addCourse") {
    $courseName = $_POST['courseName'];
    $courseDescription = $_POST['courseDescription'];
    $courseSubject = $_POST['courseSubject'];
    
    // Check if the course already exists
    $check_query = "SELECT * FROM courses WHERE course_name='$courseName'";
    $result = $conn->query($check_query);
    if ($result->num_rows > 0) {
        echo "Course already present";
    } else {
        // Insert course details into the database
        $insert_query = "INSERT INTO courses (course_name, course_description, course_subject) VALUES ('$courseName', '$courseDescription', '$courseSubject')";
        if ($conn->query($insert_query) === TRUE) {
            echo "Course added successfully.";
        } else {
            echo "Error adding the course: " . $conn->error;
        }
    }
}

// Check if the action is to delete a course
if ($_POST['action'] == "deleteCourse") {
    $courseNameToDelete = $_POST['courseNameToDelete'];
    
    // Delete the course from the tutor course requests table
    $delete_query = "DELETE FROM tutor_course_requests WHERE course_name='$courseNameToDelete'";
    if ($conn->query($delete_query) === TRUE) {
        echo "Course deleted from requests successfully.";
    } else {
        echo "Error deleting the course from requests: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
