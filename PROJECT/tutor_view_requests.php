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
    // Remove spaces from tutor name
    $tutor_name = str_replace(' ', '', $tutor_name);
    // Add more details here as needed
} else {
    // Tutor not found, redirect to index.html
    header("Location: index.html");
    exit;
}

// Fetch requests for the tutor
$request_query = "SELECT * FROM student_tutor_request WHERE REPLACE(tutor_name, ' ', '')='$tutor_name'";
$request_result = $conn->query($request_query);

// Messages variables
$success_message = "";
$error_message = "";

// Process actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $request_id = $_GET['id'];

    if ($action === 'accept') {
        $update_query = "UPDATE student_tutor_request SET status='Accepted' WHERE id='$request_id'";
    } elseif ($action === 'reject') {
        $update_query = "UPDATE student_tutor_request SET status='Rejected' WHERE id='$request_id'";
    }

    if ($conn->query($update_query) === TRUE) {
        $success_message = "Request updated successfully.";
    } else {
        $error_message = "Error updating request: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor View Requests</title>
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
            margin-left: 270px; /* Adjusted margin to make space for sidebar */
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
            border: 2px solid black; /* Border with a width of 2px and black color */
            padding: 20px; /* Padding inside the box */
            margin: 20px auto; /* Centering the box horizontally and adding space at the top */
            width: 1150px; /* Adjusted width */
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="tutor_home.php"><b><i>Home</i></b></a>
    <a href="tutor_view_courses.php">View Your Courses</a>
    <a href="tutor_take_course.php">Take a Course</a>
    <a href="tutor_delete_course.php">Remove a Course</a>
    <a href="tutor_view_requests.php"><b><i>View Student Requests</i></b></a>
    <a href="tutor_update_profile.php">Update Profile</a>
    <a href="tutor_course_request.php">Request a Course</a>
</div> 
<div class="main">
    <h2>Tutor View Requests</h2></center>
    <div class="content">
        <?php
        // Display success message if exists
        if ($success_message !== "") {
            echo "<p class='success-message'>" . $success_message . "</p>";
        }
        // Display error message if exists
        if ($error_message !== "") {
            echo "<p class='error-message'>" . $error_message . "</p>";
        }
        ?>
        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student Email</th>
                        <th>Course Name</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($request_result->num_rows > 0) {
                        while ($row = $request_result->fetch_assoc()) {
                            // Fetch student name
                            $student_email = $row['student_email'];
                            $student_query = "SELECT name FROM students WHERE email='$student_email'";
                            $student_result = $conn->query($student_query);
                            $student_name = ($student_result->num_rows > 0) ? $student_result->fetch_assoc()['name'] : "N/A";

                            // Fetch course name
                            $course_id = $row['course_id'];
                            $course_query = "SELECT course_name FROM courses WHERE id='$course_id'";
                            $course_result = $conn->query($course_query);
                            $course_name = ($course_result->num_rows > 0) ? $course_result->fetch_assoc()['course_name'] : "N/A";

                            echo "<tr>";
                            echo "<td>" . $student_name . "</td>";
                            echo "<td>" . $row['student_email'] . "</td>";
                            echo "<td>" . $course_name . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "<td>" . $row['payment_status'] . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'Pending') {
                                echo "<a href='?action=accept&id=" . $row['id'] . "' class='btn btn-success'>Accept</a>";
                                echo "<a href='?action=reject&id=" . $row['id'] . "' class='btn btn-danger'>Reject</a>";
                            } elseif ($row['status'] == 'Accepted' && $row['payment_status'] == 'Paid') {
                                // Generate URL for the course page with necessary parameters
                                $course_page_url = "tutor_course_page.php?course_id=" . $course_id . "&student_email=" . $student_email . "&tutor_name=" . $tutor_name . "&tutor_email=" . $tutor_email;
                                echo "<a href='" . $course_page_url . "' class='btn btn-primary'>Go to Course Page</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No requests found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
