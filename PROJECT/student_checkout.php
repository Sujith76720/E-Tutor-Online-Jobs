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

// Fetch course details based on course ID
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    
    // Fetch course details
    $course_query = "SELECT * FROM courses WHERE id='$course_id'";
    $course_result = $conn->query($course_query);

    if ($course_result->num_rows > 0) {
        // Course found, fetch details
        $course_row = $course_result->fetch_assoc();
        $course_name = $course_row['course_name'];
        $course_description = $course_row['course_description'];
        $course_price = $course_row['course_price'];
        // Fetch tutors for the course
        $tutors_string = $course_row['course_tutors'];
        $tutors = explode(",", $tutors_string);
    } else {
        // Course not found, redirect to student_buy_course.php
        header("Location: student_buy_course.php");
        exit;
    }
} else {
    // Redirect to student_buy_course.php if course ID is not provided
    header("Location: student_buy_course.php");
    exit;
}

// Handle tutor request submission and display relevant message
$success_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_request'])) {
    $selected_tutor = $_POST['tutor_select'];
    $course_id = $_GET['course_id'];
    
    // Check if the request already exists
    $check_query = "SELECT * FROM student_tutor_request WHERE student_email='$student_email' AND course_id='$course_id' AND tutor_name='$selected_tutor'";
    $check_result = $conn->query($check_query);
    if ($check_result->num_rows > 0) {
        // Request already exists, check status
        $request_row = $check_result->fetch_assoc();
        $request_status = $request_row['status'];
        if ($request_status == 'Pending') {
            // Request is pending
            $success_message = "Your request is being reviewed, please wait and check later.";
        } elseif ($request_status == 'Accepted') {
            // Request is accepted
            $success_message = "Request already accepted.";
        } elseif ($request_status == 'Rejected') {
            // Request is rejected
            // Allow the user to submit another request
            $success_message = "This tutor has rejected your request due to his reasons.";
        }
    } else {
        // Insert request into student_tutor_request table
        $insert_query = "INSERT INTO student_tutor_request (student_email, course_id, tutor_name) VALUES ('$student_email', '$course_id', '$selected_tutor')";
        if ($conn->query($insert_query) === TRUE) {
            $success_message = "Successfully Requested";
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Tutor</title>
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-center {
            display: flex;
            justify-content: center;
        }

        .course-details-box {
            width: 500px;
            height: 250px;
            background: linear-gradient(to right, #800080, #ff69b4);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 20px auto; /* Center the box */
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="student_home.php"><b><i>Home</i></b></a>
    <a href="student_view_courses.php">My Courses</a>
    <a href="student_buy_course.php"><b><i>Request Tutor</i></b></a>
    <a href="student_view_status.php">View Request Status</a>
    <a href="student_update_profile.php">Update Profile</a>
    <!-- Add more options here -->
</div>
<div class="main">
    <h2>Request Tutor</h2>
    <div class="content">
        <h3>Course Details:</h3>
        <div class="course-details-box">
            <p><b>Course Name:</b> <?php echo $course_name; ?></p>
            <p><b>Description:</b> <?php echo $course_description; ?></p>
            <p><b>Price:</b> ₹<?php echo $course_price; ?></p>
        </div>
    </div>
    <div class="content">
        <h3>Select a Tutor:</h3>
        <?php if (!empty($success_message)) : ?>
        <div class="alert alert-<?php echo ($success_message == "Successfully Requested") ? "success" : "danger"; ?>" role="alert">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?course_id=$course_id"; ?>">
            <div class="form-group form-center">
                <label for="tutor_select"></label>
                <select class="form-control" id="tutor_select" name="tutor_select" style="max-width: 200px;" required>
                    <?php
                    foreach ($tutors as $tutor) {
                        echo "<option value='$tutor'>$tutor</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="submit_request">Request Tutor</button>
        </form>
    </div>
</div>
</center>
</body>
</html>