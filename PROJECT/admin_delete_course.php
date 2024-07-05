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

// Establish a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = ""; // If you have set a password, provide it here
$database = "software"; // Replace "your_database_name" with the actual name of your database
$conn = new mysqli($servername, $username, $password, $database);

// Initialize variables to hold potential error messages
$delete_course_message = "";

// Check if the form for deleting a course has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract course name to delete
    $courseNameToDelete = $_POST['courseNameToDelete'];

    // Prepare SQL query to check if the course exists
    $check_query = "SELECT * FROM courses WHERE course_name='$courseNameToDelete'";
    $result = $conn->query($check_query);

    // Check if the course exists
    if ($result->num_rows > 0) {
        // Course exists, proceed with deletion
        // Prepare SQL query to delete the course
        $delete_query = "DELETE FROM courses WHERE course_name='$courseNameToDelete'";
        if ($conn->query($delete_query) === TRUE) {
            $delete_course_message = "Course deleted successfully.";
        } else {
            $delete_course_message = "Error deleting the course: " . $conn->error;
        }
    } else {
        // Course not found, display message
        $delete_course_message = "Course with this name not present in the table.";
    }
}

// Fetch courses from the database based on search query
$search_query = isset($_GET['searchCourse']) ? $_GET['searchCourse'] : '';
if (!empty($search_query)) {
    $search_query = $conn->real_escape_string($search_query);
    $courses_query = "SELECT * FROM courses WHERE course_name LIKE '%$search_query%'";
} else {
    $courses_query = "SELECT * FROM courses";
}
$courses_result = $conn->query($courses_query);

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
            border: 2px solid #ccc;
            /* Border color */
            border-radius: 5px;
            /* Border radius to make corners round */
            padding: 20px;
            /* Padding inside the container */
            width: 1000px;
            /* Width of the container */
            /* height: 90%; */
            margin: 20px auto;
            /* Center the container horizontally */
            background-color: #f9f9f9;
            /* Background color */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* Box shadow for a slight depth effect */
        }

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>

<center>

    <div class="sidebar">
        <a href="admin_home.php">Home</a>
        <a href="admin_add_course.php">Add Course</a>
        <a href="admin_delete_course.php"><b><i>Delete Course</i></b></a>
        <a href="admin_tutors_list.php">Tutors List</a>
        <a href="admin_course_list.php">Courses List</a>
        <a href="admin_view_student_feedback.php">Student Feedback</a>
        <a href="admin_tutor_course_requests.php">Tutor Course Requests</a>
        <!-- Add more actions here -->
    </div>

<body>

    <div class="container">
        <!-- <h2>Delete Course</h2>
        <div class="box">
            <form action="admin_delete_course.php" method="post">
                <div class="form-group">
                    <label for="courseNameToDelete">Course Name to Delete:</label>
                    <input type="text" class="form-control" id="courseNameToDelete" name="courseNameToDelete" required>
                </div>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <p><?php echo $delete_course_message; ?></p>
        </div> -->

        <h2>Course List</h2>
        <div class="box">
            <form action="admin_delete_course.php" method="get">
                <div class="form-group">
                    <label for="searchCourse">Search Course:</label>
                    <input type="text" class="form-control" id="searchCourse" name="searchCourse" value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <br>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Subject</th>
                        <th>Tutors</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($courses_result->num_rows > 0) {
                        while ($row = $courses_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['course_name'] . "</td>";
                            echo "<td>" . $row['course_description'] . "</td>";
                            echo "<td>" . $row['course_subject'] . "</td>";
                            echo "<td><pre>" . str_replace(",", "<br>", $row['course_tutors']) . "</pre></td>";
                            echo "<td>" . $row['course_price'] . "</td>";
                            echo "<td><form action='admin_delete_course.php' method='post'><input type='hidden' name='courseNameToDelete' value='" . $row['course_name'] . "'><button type='submit' class='delete-btn'>Delete</button></form></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No courses found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
