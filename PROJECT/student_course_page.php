<?php
session_start();

// Check if student is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.html"); // Redirect to index.html if not logged in
    exit;
}

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

// Fetch course details based on provided parameters
if(isset($_GET['course_id']) && isset($_GET['course_name']) && isset($_GET['student_name']) && isset($_GET['student_email']) && isset($_GET['tutor_name'])) {
    $course_id = $_GET['course_id'];
    $course_name = $_GET['course_name'];
    $student_name = $_GET['student_name'];
    $student_email = $_GET['student_email'];
    $tutor_name = $_GET['tutor_name'];

    // Fetch course details from course_details table
    $course_query = "SELECT * FROM course_details WHERE course_id='$course_id' AND course_name='$course_name' AND student_name='$student_name' AND student_email='$student_email' AND tutor_name='$tutor_name'";
    $course_result = $conn->query($course_query);

    if ($course_result->num_rows > 0) {
        $course_row = $course_result->fetch_assoc();
        $class_datetime = $course_row['class_datetime'];
        $meeting_link = $course_row['meeting_link'];
        $feedback = $course_row['student_feedback'];
    } else {
        // Course details not found, handle error
        $error_message = "The details for this course are not yet updated. Please recheck later.";
    }
} else {
    // Parameters missing, handle error
    $error_message = "Missing parameters.";
}

// Process feedback form submission
if(isset($_POST['submit_feedback'])) {
    $new_feedback = $_POST['feedback'];

    // Check if feedback is empty before updating
    if(empty($feedback)) {
        // Update course_details table with new feedback
        $update_query = "UPDATE course_details SET student_feedback='$new_feedback' WHERE course_id='$course_id' AND course_name='$course_name' AND student_name='$student_name' AND student_email='$student_email' AND tutor_name='$tutor_name'";
        if ($conn->query($update_query) === TRUE) {
            $feedback = $new_feedback; // Update feedback displayed on the page
            $success_message = "Feedback submitted successfully.";
        } else {
            $error_message = "Error updating feedback: " . $conn->error;
        }
    } else {
        $error_message = "Feedback has already been submitted and cannot be updated.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Course Page</title>
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
            width: 1100px;
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
    <a href="student_view_status.php"><b><i>View Request Status</i></b></a>
    <a href="student_update_profile.php">Update Profile</a>
    <!-- Add more options here -->
</div>

<div class="container">
    <h2>Course Details</h2>
    <?php
    if(isset($error_message)) {
        echo "<p class='error-message'>" . $error_message . "</p>";
    } else {
    ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student Name: <?php echo $student_name; ?></h5>
                    <p class="card-text">Student Email: <?php echo $student_email; ?></p>
                    <h5 class="card-title">Tutor Name: <?php echo $tutor_name; ?></h5>
                    <p class="card-text">Course ID: <?php echo $course_id; ?></p>
                    <p class="card-text">Course Name: <?php echo $course_name; ?></p>
                    <p class="card-text">Class Date and Time: <?php echo $class_datetime; ?></p>
                    <p class="card-text">Meeting Link: <a href="<?php echo $meeting_link; ?>" target="_blank"><?php echo $meeting_link; ?></a></p>
                    <hr>
                    <?php if(empty($feedback)) { ?>
                    <h5 class="card-title">Feedback:</h5>
                    <form method="POST" action="">
                        <div class="form-group">
                            <textarea class="form-control" name="feedback" rows="3" placeholder="Enter your feedback here"><?php echo isset($feedback) ? $feedback : ''; ?></textarea>
                        </div>
                        <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
                    </form>
                    <?php } else { ?>
                    <p class="card-text">Feedback: <?php echo $feedback; ?></p>
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
<a href="student_view_status.php" class="btn btn-primary" style="position: absolute;">Back</a>
</center>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
