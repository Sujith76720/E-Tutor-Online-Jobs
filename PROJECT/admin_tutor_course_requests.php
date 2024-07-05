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
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<center>
<div class="sidebar">
    <a href="admin_home.php">Home</a>
    <a href="admin_add_course.php">Add Course</a>
    <a href="admin_delete_course.php">Delete Course</a>
    <a href="admin_tutors_list.php">Tutors List</a>
    <a href="admin_course_list.php">Courses List</a>
    <a href="admin_view_student_feedback.php">Student Feedback</a>
    <a href="admin_tutor_course_requests.php"><b><i>Tutor Course Requests</i></b></a>
    <!-- Add more actions here -->
</div>

<body>
    
    <div class="container">
        <div class="box">
        <h2>Tutor Course Requests</h2>
        <div id="messages">
            <!-- Message area to display success or error messages -->
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Tutor Name</th>
                    <th>Course Name</th>
                    <th>Course Description</th>
                    <th>Course Subject</th>
                    <th>Request Date</th>
                    <th>Action</th> <!-- New column for actions -->
                </tr>
            </thead>
            <tbody>
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

                // Fetch tutor course requests
                $tutor_course_request_query = "SELECT * FROM tutor_course_requests";
                $tutor_course_request_result = $conn->query($tutor_course_request_query);

                while ($row = $tutor_course_request_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['tutor_name'] . "</td>";
                    echo "<td>" . $row['course_name'] . "</td>";
                    echo "<td>" . $row['course_description'] . "</td>";
                    echo "<td>" . $row['course_subject'] . "</td>";
                    echo "<td>" . $row['request_date'] . "</td>";
                    echo "<td>";
                    echo "<form action='' method='post'>";
                    echo "<input type='hidden' name='courseName' value='" . $row['course_name'] . "'>";
                    // Check if the course already exists in the courses table
                    $check_query = "SELECT * FROM courses WHERE course_name='" . $row['course_name'] . "'";
                    $result = $conn->query($check_query);
                    if ($result->num_rows > 0) {
                        echo "<span class='text-danger'>Course already added</span>";
                    } else {
                        // Allow admin to add price before adding the course
                        echo "<input type='number' name='coursePrice' placeholder='Enter course price' required>";
                        echo "<button type='submit' name='addAction' class='btn btn-primary'>Add Course</button> ";
                    }
                    echo "<button type='submit' name='deleteAction' class='btn btn-danger'>Delete Course</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }

                // Process actions
                if (isset($_POST['addAction'])) {
                    $courseName = $_POST['courseName'];
                    $coursePrice = $_POST['coursePrice']; // Get the entered course price
                    // Add the course to the courses table
                    $insert_query = "INSERT INTO courses (course_name, course_description, course_subject, course_price) SELECT course_name, course_description, course_subject, '$coursePrice' FROM tutor_course_requests WHERE course_name='$courseName'";
                    if ($conn->query($insert_query) === TRUE) {
                        echo "<script>document.getElementById('messages').innerHTML += '<p class=\"text-success\">Course added successfully</p>';</script>";
                    } else {
                        echo "<script>document.getElementById('messages').innerHTML += '<p class=\"text-danger\">Error adding course</p>';</script>";
                    }
                    header("Location: ".$_SERVER['PHP_SELF']); // Redirect to the same page after performing the action
                    exit();
                }

                if (isset($_POST['deleteAction'])) {
                    $courseName = $_POST['courseName'];
                    // Delete the course from tutor_course_requests table
                    $delete_query = "DELETE FROM tutor_course_requests WHERE course_name='$courseName'";
                    if ($conn->query($delete_query) === TRUE) {
                        echo "<script>document.getElementById('messages').innerHTML += '<p class=\"text-success\">Course successfully deleted from requests</p>';</script>";
                    } else {
                        echo "<script>document.getElementById('messages').innerHTML += '<p class=\"text-danger\">Error deleting course from requests</p>';</script>";
                    }
                    header("Location: ".$_SERVER['PHP_SELF']); // Redirect to the same page after performing the action
                    exit();
                }

                // Close the connection
                $conn->close();
                ?>
            </tbody>
        </table>
            </div>
    </div>
            </center>
</body>
</html>
