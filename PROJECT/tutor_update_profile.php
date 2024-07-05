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
    $tutor_email = $tutor_row['email'];
    $tutor_subject = $tutor_row['subject'];
    // Add more details here as needed
} else {
    // Tutor not found, redirect to login page
    header("Location: login_process.php");
    exit;
}

// Handle form submission for updating profile details
if (isset($_POST['updateName'])) {
    // Extract new name from the form submission
    $new_name = $_POST['new_name'];
    
    // Prepare SQL query to update tutor's name
    $update_name_query = "UPDATE tutors SET name='$new_name' WHERE email='$tutor_email'";
    
    if ($conn->query($update_name_query) === TRUE) {
        // Update successful
        $update_name_message = "Name updated successfully.";
        // Update the stored tutor name in the session variable and the displayed name
        $_SESSION['tutor_name'] = $new_name;
        $tutor_name = $new_name; // Update the displayed name immediately
    } else {
        // Update failed
        $update_name_message = "Error updating name: " . $conn->error;
    }
}

// Handle form submission for updating tutor's email
if (isset($_POST['updateEmail'])) {
    // Extract new email from the form submission
    $new_email = $_POST['new_email'];
    
    // Prepare SQL query to update tutor's email
    $update_email_query = "UPDATE tutors SET email='$new_email' WHERE email='$tutor_email'";
    
    if ($conn->query($update_email_query) === TRUE) {
        // Update successful
        $update_email_message = "Email updated successfully.";
        // Update the stored tutor email in the session variable and the displayed email
        $_SESSION['email'] = $new_email;
        $tutor_email = $new_email; // Update the displayed email immediately
    } else {
        // Update failed
        $update_email_message = "Error updating email: " . $conn->error;
    }
}

// Handle form submission for updating tutor's subject
if (isset($_POST['updateSubject'])) {
    // Extract new subject from the form submission
    $new_subject = $_POST['new_subject'];
    
    // Prepare SQL query to update tutor's subject
    $update_subject_query = "UPDATE tutors SET subject='$new_subject' WHERE email='$tutor_email'";
    
    if ($conn->query($update_subject_query) === TRUE) {
        // Update successful
        $update_subject_message = "Subject updated successfully.";
        // Update the stored tutor subject in the session variable and the displayed subject
        $_SESSION['tutor_subject'] = $new_subject;
        $tutor_subject = $new_subject; // Update the displayed subject immediately
    } else {
        // Update failed
        $update_subject_message = "Error updating subject: " . $conn->error;
    }
}

// Handle form submission for changing password
if (isset($_POST['changePassword'])) {
    // Extract password details from the form submission
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    
    // Check if the new password meets the strong password criteria
    if (strlen($new_password) < 6 ||
        !preg_match("/[A-Z]/", $new_password) ||
        !preg_match("/[a-z]/", $new_password) ||
        !preg_match("/\d/", $new_password) ||
        !preg_match("/\W/", $new_password)) {
        $password_change_message = "Password should be at least 6 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character.";
    } else {
        // Prepare SQL query to fetch the tutor's password
        $password_query = "SELECT password FROM tutors WHERE email='$tutor_email'";
        $password_result = $conn->query($password_query);
        
        if ($password_result->num_rows > 0) {
            $row = $password_result->fetch_assoc();
            $stored_password = $row['password'];
            // Verify if the current password matches the stored password
            if ($current_password === $stored_password) {
                // Verify if the new password and confirm new password match
                if ($new_password === $confirm_new_password) {
                    // Update the tutor's password in the database
                    $update_password_query = "UPDATE tutors SET password='$new_password' WHERE email='$tutor_email'";
                    if ($conn->query($update_password_query) === TRUE) {
                        $password_change_message = "Password changed successfully.";
                    } else {
                        $password_change_message = "Error changing password: " . $conn->error;
                    }
                } else {
                    $password_change_message = "New password and confirm new password did not match.";
                }
            } else {
                $password_change_message = "Incorrect current password.";
            }
        } else {
            $password_change_message = "Error fetching stored password.";
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
    <title>Update Profile</title>
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

    </style>
</head>
<body>
<center>
<div class="sidebar">
    <a href="tutor_home.php">Home</a>
    <a href="tutor_view_courses.php">View Your Courses</a>
    <a href="tutor_take_course.php">Take a Course</a>
    <a href="tutor_delete_course.php">Remove a Course</a>
    <a href="tutor_view_requests.php">View Student Requests</a>
    <a href="tutor_update_profile.php"><b><i>Update Profile</i></b></a>
    <a href="tutor_course_request.php">Request a Course</a>
    <!-- Add more actions here -->
</div>

<div class="main">
    <div class="container">
        <h1>Update Profile</h1>
        <h3>Welcome <?php echo $tutor_name; ?></h3>

        <!-- Update Name Form -->
        <form action="" method="post">
            <div class="form-group">
                <label for="new_name">Name</label>
                <input type="text" class="form-control" id="new_name" name="new_name" value="<?php echo $tutor_name; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="updateName">Update Name</button>
        </form>
        <p class="message"><?php echo isset($update_name_message) ? $update_name_message : ""; ?></p>

        <!-- Update Email Form -->
        <form action="" method="post">
            <div class="form-group">
                <label for="new_email">Email</label>
                <input type="email" class="form-control" id="new_email" name="new_email" value="<?php echo $tutor_email; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="updateEmail">Update Email</button>
        </form>
        <p class="message"><?php echo isset($update_email_message) ? $update_email_message : ""; ?></p>

        <!-- Update Subject Form -->
        <form action="" method="post">
            <div class="form-group">
                <label for="new_subject">Subject</label>
                <input type="text" class="form-control" id="new_subject" name="new_subject" value="<?php echo $tutor_subject; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="updateSubject">Update Subject</button>
        </form>
        <p class="message"><?php echo isset($update_subject_message) ? $update_subject_message : ""; ?></p>

        <!-- Change Password Form -->
        <form action="" method="post">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_new_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="changePassword">Change Password</button>
        </form>
        <p class="message"><?php echo isset($password_change_message) ? $password_change_message : ""; ?></p>
    </div>
</div>
</center>
</body>
</html>
