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

// Handle search functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    // Search for courses based on the course name
    $search_courses_query = "SELECT * FROM courses WHERE course_name LIKE '%$search_query%'";
    $search_courses_result = $conn->query($search_courses_query);
} else {
    // Fetch all courses if no search query is provided
    $search_courses_query = "SELECT * FROM courses WHERE course_tutors != ''";
    $search_courses_result = $conn->query($search_courses_query);
}

// Close the connection
$conn->close();
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
            width: 45%; /* Width adjusted for two columns */
            background: linear-gradient(to right, #800080, #ff69b4);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
            float: left; /* Align boxes horizontally */
            margin-right: 5%; /* Add some space between boxes */
        }

        .student-box:last-child {
            margin-right: 0; /* Remove margin for the last box in a row */
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
    </style>
    <a href="?logout=true" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Logout</a>
</head>
<body>
<center>
<div class="sidebar">
    <a href="student_home.php">Home</a>
    <a href="student_view_courses.php">My Courses</a>
    <a href="student_buy_course.php"><b><i>Request Tutor</i></b></a>
    <a href="student_view_status.php">View Request Status</a>
    <a href="student_update_profile.php">Update Profile</a>
    <!-- Add more options here -->
</div>

<div class="main">
    <h2>Hello <?php echo $student_name; ?></h2>
    <!-- Search form -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="search_query">Search Courses:</label>
            <input type="text" class="form-control" id="search_query" name="search_query" placeholder="Enter course name">
        </div>
        <button type="submit" class="btn btn-primary" name="search">Search</button>
    </form>
    <br>
    <!-- Display search results -->
    <div class="content">
        <h3>Available Courses:</h3>
        <?php
        if ($search_courses_result->num_rows > 0) {
            // Output data of each row
            $count = 0;
            while($row = $search_courses_result->fetch_assoc()) {
                if ($count % 2 == 0) {
                    echo "<div class='row'>";
                }
                echo "<div class='student-box'>";
                echo "<h4>" . $row["course_name"] . "</h4>";
                echo "<p>" . $row["course_description"] . "</p>";
                echo "<p><b>Price:</b> ₹" . $row["course_price"] . "</p>";
                // Add buy button or link here
                echo "<a href='student_checkout.php?course_id=".$row["id"]."' class='btn btn-primary'>Buy Course</a>";
                echo "</div>";
                $count++;
                if ($count % 2 == 0) {
                    echo "</div>";
                }
            }
            if ($count % 2 != 0) {
                echo "</div>"; // Close the row if the last row has odd number of courses
            }
        } else {
            echo "No courses available.";
        }
        ?>
    </div>
</div>
</center>
</body>
</html>

