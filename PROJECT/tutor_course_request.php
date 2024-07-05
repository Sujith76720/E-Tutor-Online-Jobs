<?php
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

// Initialize variables to hold potential error messages
$request_message = "";

// Check if the form for requesting a course has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['requestCourse'])) {
    // Extract course details from the form submission
    $courseName = $_POST['courseName'];
    $courseDescription = $_POST['courseDescription'];
    $courseSubject = $_POST['courseSubject'];
    
    // Check if the course already exists in the courses table
    $check_query = "SELECT * FROM courses WHERE course_name='$courseName'";
    $result = $conn->query($check_query);
    
    if ($result->num_rows > 0) {
        // Course already exists, display message
        $request_message = "<span style='color: red;'>Course already present.</span>";
    } else {
        // Course does not exist, proceed with adding to tutor_course_requests table
        // Prepare SQL query to insert the course request into the database
        $insert_query = "INSERT INTO tutor_course_requests (tutor_name, course_name, course_description, course_subject) 
                         VALUES ('$tutor_name', '$courseName', '$courseDescription', '$courseSubject')";
        
        if ($conn->query($insert_query) === TRUE) {
            $request_message = "Course request submitted successfully.";
        } else {
            $request_message = "Error submitting the course request: " . $conn->error;
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
    <title>Tutor Course Request</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
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
            border: 2px solid black; /* Border with a width of 2px and black color */
            padding: 20px; /* Padding inside the box */
            margin: 20px; /* Margin around the box */
            width: 1000px;
        }

        .table thead th {
            background-color: #343a40; /* Dark Gray */
            color: white;
        }

        .table tbody tr {
            border-bottom: 5px solid #343a40; /* Dark Gray */
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        /* CSS for the table */
        table {
            width: 100%;
            border-collapse: collapse; /* Collapse the borders into a single border */
        }

        /* CSS for table headings */
        th {
            border: 5px solid #343a40; /* Dark Gray */
            padding: 8px; /* Add padding to the table headings */
        }

        /* CSS for table cells */
        td {
            border: 5px solid #343a40; /* Dark Gray */
            padding: 8px; /* Add padding to the table cells */
        }

        .row{
            align-self: center;
            
        }

    </style>
</head>
<body>
<center>
<div class="container">
<div class="sidebar">
    <a href="tutor_home.php">Home</a>
    <a href="tutor_view_courses.php">View Your Courses</a>
    <a href="tutor_take_course.php">Take a Course</a>
    <a href="tutor_delete_course.php">Remove a Course</a>
    <a href="tutor_view_requests.php">View Student Requests</a>
    <a href="tutor_update_profile.php">Update Profile</a>
    <a href="tutor_course_request.php"><b><i>Request a Course</i></b></a>
    <!-- Add more actions here -->
</div>

    <div class="row">
        <div class="col-md-6 offset-md-3 mt-5">
            <h2 class="text-center mb-4">Tutor Course Request</h2>
            <?php if (!empty($request_message)) : ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $request_message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="courseName">Course Name</label>
                    <input type="text" class="form-control" id="courseName" name="courseName" required>
                </div>
                <div class="form-group">
                    <label for="courseDescription">Course Description</label>
                    <textarea class="form-control" id="courseDescription" name="courseDescription" required></textarea>
                </div>
                <div class="form-group">
                    <label for="courseSubject">Course Subject</label>
                    <input type="text" class="form-control" id="courseSubject" name="courseSubject" required>
                </div>
                <button type="submit" class="btn btn-primary" name="requestCourse">Submit</button>
            </form>
        </div>
    </div>
</div>
</center>

</body>
</html>
