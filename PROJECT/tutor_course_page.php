<?php
// Start session
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

// Fetch student name
$student_email = $_GET['student_email'];
$student_query = "SELECT name FROM students WHERE email='$student_email'";
$student_result = $conn->query($student_query);

if ($student_result->num_rows > 0) {
    // Student found, fetch name
    $student_row = $student_result->fetch_assoc();
    $student_name = $student_row['name'];
} else {
    // Student not found, handle error or set default value
    $student_name = "N/A";
}

// Fetch parameters from URL
if(isset($_GET['course_id']) && isset($_GET['student_email']) && isset($_GET['tutor_name']) && isset($_GET['tutor_email'])) {
    $course_id = $_GET['course_id'];
    $student_email = $_GET['student_email'];
    $tutor_name = $_GET['tutor_name'];
    $tutor_email = $_GET['tutor_email'];

    // Check if data already exists in course_details table
    $check_query = "SELECT * FROM course_details WHERE student_email='$student_email' AND tutor_email='$tutor_email' AND course_id='$course_id'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Data exists, fetch details
        $course_row = $check_result->fetch_assoc();
        $student_name = $course_row['student_name'];
        $course_name = $course_row['course_name'];
        $class_datetime = $course_row['class_datetime'];
        $meeting_link = $course_row['meeting_link'];
        $feedback = $course_row['student_feedback'];
        // Add more details here as needed
    } else {
        // Data not found, display form for insertion
        $show_form = true;
        // Initialize $course_name to avoid undefined variable error
        $course_name = "";

        // Fetch course name from courses table
        $course_query = "SELECT course_name FROM courses WHERE id='$course_id'";
        $course_result = $conn->query($course_query);
        if ($course_result->num_rows > 0) {
            $course_row = $course_result->fetch_assoc();
            $course_name = $course_row['course_name'];
        }
    }
} else {
    // Parameters missing, handle error
    $error_message = "Missing parameters.";
}

// Success and error messages
$success_message = "";
$error_message = "";

// Check if form submitted
if(isset($_POST['submit'])) {
    $class_datetime = $_POST['class_datetime'];
    $meeting_link = $_POST['meeting_link'];

    // Insert data into course_details table
    $insert_query = "INSERT INTO course_details (student_name, student_email, tutor_name, tutor_email, course_id, course_name, class_datetime, meeting_link) 
                    VALUES ('$student_name', '$student_email', '$tutor_name', '$tutor_email', '$course_id', '$course_name', '$class_datetime', '$meeting_link')";

    if ($conn->query($insert_query) === TRUE) {
        $success_message = "Data successfully stored.";
        $show_form = false; // Hide the form after successful insertion
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

// Check if feedback is present
$show_feedback_form = empty($feedback);
$feedback_exists = !$show_feedback_form && isset($feedback);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
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
        .container {
            text-align: center;
            /* border: 2px solid black; Border with a width of 2px and black color */
            padding: 20px; /* Padding inside the box */
            margin: 50px auto; /* Margin around the box */
            /* max-width: 600px; Maximum width of the container */
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
<body>
<center>
<div class="sidebar">
    <a href="tutor_home.php">Home</a>
    <a href="tutor_view_courses.php">View Your Courses</a>
    <a href="tutor_take_course.php">Take a Course</a>
    <a href="tutor_delete_course.php">Remove a Course</a>
    <a href="tutor_view_requests.php"><b><i>View Student Requests</i></b></a>
    <a href="tutor_update_profile.php">Update Profile</a>
    <a href="tutor_course_request.php">Request a Course</a>
    <!-- Add more actions here -->
</div>
<h2>Course Details</h2>
<div class="container">
    
    <?php
    // Display error message if exists
    if (isset($error_message) && !empty($error_message)) {
        echo "<p class='error-message'>" . $error_message . "</p>";
    } elseif (isset($success_message) && !empty($success_message)) {
        echo "<p class='success-message'>" . $success_message . "</p>";
    }

    if(isset($show_form) && $show_form) {
    ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <!-- Display the form for insertion -->
                    <form method="post">
                        <div class="form-group">
                            <label for="class_datetime">Class Date and Time:</label>
                            <input type="datetime-local" class="form-control" id="class_datetime" name="class_datetime">
                        </div>
                        <div class="form-group">
                            <label for="meeting_link">Meeting Link:</label>
                            <input type="url" class="form-control" id="meeting_link" name="meeting_link">
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } else { ?>
    <!-- Display course details -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student Name: <?php echo $student_name; ?></h5>
                    <p class="card-text">Student Email: <?php echo $student_email; ?></p>
                    <p class="card-text">Tutor Name: <?php echo $tutor_name; ?></p>
                    <p class="card-text">Tutor Email: <?php echo $tutor_email; ?></p>
                    <p class="card-text">Course ID: <?php echo $course_id; ?></p>
                    <p class="card-text">Course Name: <?php echo $course_name; ?></p>
                    <p class="card-text">Class Date and Time: <?php echo $class_datetime; ?></p>
                    <p class="card-text">Meeting Link: <a href="<?php echo $meeting_link; ?>" target="_blank"><?php echo $meeting_link; ?></a></p>
                    <!-- Add more details here as needed -->
                    <?php if ($feedback_exists) { ?>
                        <hr>
                        <h5 class="card-title">Feedback:</h5>
                        <p class="card-text">Feedback: <?php echo $feedback; ?></p>
                    <?php } elseif ($show_feedback_form) { ?>
                        <hr>
                        <h5 class="card-title">Feedback:</h5>
                        <p class="card-text">No feedback available.</p>
                    <?php } ?>
                    <?php
                    if(isset($success_message)) {
                        echo "<p class='success-message'>" . $success_message . "</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<a href="tutor_view_requests.php" class="btn btn-primary" style="position: absolute;">Back</a>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
