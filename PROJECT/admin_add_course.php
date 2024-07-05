<?php

// Check if the user is not logged in, then redirect to index.html
// if (!isset($_SESSION['email'])) {
//     header("Location: index.html");
//     exit;
// }

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy session data
    header("Location: index.html"); // Redirect to index.html after logout
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
$database = "software"; // Replace "your_database_name" with the actual name of your database
$conn = new mysqli($servername, $username, $password, $database);

// Initialize variables to hold potential error messages
$add_course_message = "";

// Check if the form for adding a course has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract course details from the form submission
    $courseName = $_POST['courseName'];
    $courseDescription = $_POST['courseDescription'];
    $courseSubject = $_POST['courseSubject'];
    $coursePrice = $_POST['coursePrice'];

    // Ensure course price is not negative
    if ($coursePrice < 1) {
        $add_course_message = "Course price must start from 1.";
    } else {
        // Prepare SQL query to check if the course already exists
        $check_query = "SELECT * FROM courses WHERE course_name='$courseName'";
        $result = $conn->query($check_query);

        // Check if the course already exists
        if ($result->num_rows > 0) {
            $add_course_message = "Course with the same name already exists.";
        } else {
            // Insert course details into the database
            $insert_query = "INSERT INTO courses (course_name, course_description, course_subject, course_price) VALUES ('$courseName', '$courseDescription', '$courseSubject', '$coursePrice')";
            if ($conn->query($insert_query) === TRUE) {
                $add_course_message = "Course added successfully.";
            } else {
                $add_course_message = "Error adding the course: " . $conn->error;
            }
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
    <title>Admin Home</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
    <style>
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;  
            background-color: #111;
            padding-top: 100px;
        }


        .sidebar a {
            padding: 20px 15px;
            font-size: 25px;
            color: #818181;
            display: block;
        }

        .sidebar a:hover {
            color: #f1f1f1;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
        }

        .hidden {
            display: none;
        }

        .box {
            border: 2px solid #ccc; /* Border color */
            border-radius: 5px; /* Border radius to make corners round */
            padding: 20px; /* Padding inside the container */
            width: 1000px; /* Width of the container */
            /* height: 90%; */
            margin: 20px auto; /* Center the container horizontally */
            background-color: #f9f9f9; /* Background color */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Box shadow for a slight depth effect */
        }
    </style>
    <!-- <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a> -->
</head>
<center>
<div class="sidebar">
    <a href="admin_home.php">Home</a>
    <a href="admin_add_course.php"><b><i>Add Course</i></b></a>
    <a href="admin_delete_course.php">Delete Course</a>
    <a href="admin_tutors_list.php">Tutors List</a>
    <a href="admin_course_list.php">Courses List</a>
    <a href="admin_view_student_feedback.php">Student Feedback</a>
    <a href="admin_tutor_course_requests.php">Tutor Course Requests</a>
    <!-- Add more actions here -->
</div>

<body>
    
    <div class="container">
        <h2>Add Course</h2>
        <div class="box">
        <form action="admin_add_course.php" method="post">
            <div class="form-group">
                <label for="courseName">Course Name:</label>
                <input type="text" class="form-control" id="courseName" name="courseName" required>
            </div>
            <div class="form-group">
                <label for="courseDescription">Course Description:</label>
                <textarea class="form-control" id="courseDescription" name="courseDescription" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="courseSubject">Course Subject:</label>
                <input type="text" class="form-control" id="courseSubject" name="courseSubject" required>
            </div>
            <div class="form-group">
                <label for="coursePrice">Course Price:</label>
                <input type="number" class="form-control" id="coursePrice" name="coursePrice" required min="1" pattern="\d+" title="Please enter a valid positive number">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        </div>
        <p><?php echo $add_course_message; ?></p>
    </div>
</center>
</body>
</html>
