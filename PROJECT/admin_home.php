<?php
// Start or resume session
session_start();

// Check if the user is not logged in, then redirect to index.html
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
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

// Fetch number of students
$student_query = "SELECT COUNT(*) as total FROM students";
$student_result = $conn->query($student_query);
$student_row = $student_result->fetch_assoc();
$student_count = $student_row['total'];

// Fetch number of tutors
$tutor_query = "SELECT COUNT(*) as total FROM tutors";
$tutor_result = $conn->query($tutor_query);
$tutor_row = $tutor_result->fetch_assoc();
$tutor_count = $tutor_row['total'];

// Fetch number of courses
$course_query = "SELECT COUNT(*) as total FROM courses";
$course_result = $conn->query($course_query);
$course_row = $course_result->fetch_assoc();
$course_count = $course_row['total'];

// Initialize variables to hold potential error messages
$add_course_message = "";
$delete_course_message = "";

// Check if the form for adding a course has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addCourse'])) {
        // Extract course details from the form submission
        $courseName = $_POST['courseName'];
        $courseDescription = $_POST['courseDescription'];
        $courseSubject = $_POST['courseSubject'];
        
        // Prepare SQL query to check if the course already exists
        $check_query = "SELECT * FROM courses WHERE course_name='$courseName'";
        $result = $conn->query($check_query);
        
        // Check if the course already exists
        if ($result->num_rows > 0) {
            $add_course_message = "Course with the same name already exists.";
        } else {
            // Insert course details into the database
            $insert_query = "INSERT INTO courses (course_name, course_description, course_subject) VALUES ('$courseName', '$courseDescription', '$courseSubject')";
            if ($conn->query($insert_query) === TRUE) {
                $add_course_message = "Course added successfully.";
                // Update course count
                $course_count++;
            } else {
                $add_course_message = "Error adding the course: " . $conn->error;
            }
        }
    } elseif (isset($_POST['deleteCourse'])) {
        // Extract course name to delete
        $courseNameToDelete = $_POST['courseNameToDelete'];
        
        // Prepare SQL query to delete the course
        $delete_query = "DELETE FROM courses WHERE course_name='$courseNameToDelete'";
        if ($conn->query($delete_query) === TRUE) {
            $delete_course_message = "Course deleted successfully.";
            // Update course count
            $course_count--;
        } else {
            $delete_course_message = "Error deleting the course: " . $conn->error;
        }
    }
}

// Fetch tutor course requests
$tutor_course_request_query = "SELECT * FROM tutor_course_requests";
$tutor_course_request_result = $conn->query($tutor_course_request_query);

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
    </style>
</head>
<body>
<center>
<div class="sidebar">
    <a href="admin_home.php"><i><b>Home</i></b></a>
    <a href="admin_add_course.php">Add Course</a>
    <a href="admin_delete_course.php">Delete Course</a>
    <a href="admin_tutors_list.php">Tutors List</a>
    <a href="admin_course_list.php">Courses List</a>
    <a href="admin_view_student_feedback.php">Student Feedback</a>
    <a href="admin_tutor_course_requests.php">Tutor Course Requests</a>
    <!-- Add more actions here -->
</div>

<div class="main">
    
    <h2>Welcome Admin</h2>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Number of Students</h5>
                        <p class="card-text" id="studentCount"><?php echo $student_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Number of Tutors</h5>
                        <p class="card-text" id="tutorCount"><?php echo $tutor_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Number of Courses</h5>
                        <p class="card-text" id="courseCount"><?php echo $course_count; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </center>
</body>
</html>
