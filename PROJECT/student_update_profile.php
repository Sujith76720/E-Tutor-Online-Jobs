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

// Initialize error message variable
$error_message = "";

// Fetch student details using the logged-in email
$student_email = $_SESSION['email'];
$student_query = "SELECT * FROM students WHERE email='$student_email'";
$student_result = $conn->query($student_query);

if ($student_result->num_rows > 0) {
    // Student found, fetch details
    $student_row = $student_result->fetch_assoc();
    $student_name = $student_row['name'];
    $student_email = $student_row['email'];
} else {
    // Student not found, redirect to index.html
    header("Location: index.html");
    exit;
}

// Check if the form for updating profile is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['updateProfile'])) {
        // Get form data
        $newName = $_POST['newName'];
        $newEmail = $_POST['newEmail'];

        // Update the name and email in the database
        $update_query = "UPDATE students SET name='$newName', email='$newEmail' WHERE email='$student_email'";
        if ($conn->query($update_query) === TRUE) {
            $error_message = "Profile updated successfully.";
            // Update session email if changed
            $_SESSION['email'] = $newEmail;
            // Update displayed name and email
            $student_name = $newName;
            $student_email = $newEmail;
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
    } elseif (isset($_POST['updatePassword'])) {
        // Get form data
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmNewPassword = $_POST['confirmNewPassword'];

        // Fetch student details from the database
        $student_query = "SELECT * FROM students WHERE email='$student_email'";
        $student_result = $conn->query($student_query);

        if ($student_result->num_rows > 0) {
            $student_row = $student_result->fetch_assoc();
            $storedPassword = $student_row['password'];

            // Check if the current password matches the stored password
            if ($currentPassword != $storedPassword) {
                $error_message = "Incorrect current password";
            } else {
                // Check if the new password and confirm new password match
                if ($newPassword != $confirmNewPassword) {
                    $error_message = "New password and confirm new password don't match";
                } else {
                    // Validate the new password
                    $uppercase = preg_match('@[A-Z]@', $newPassword);
                    $lowercase = preg_match('@[a-z]@', $newPassword);
                    $number    = preg_match('@[0-9]@', $newPassword);
                    $specialChars = preg_match('@[^\w]@', $newPassword);

                    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($newPassword) < 6) {
                        $error_message = "Invalid new password. It must contain at least 6 characters, including uppercase, lowercase, digit, and special character.";
                    } else {
                        // Update the password in the database
                        $update_password_query = "UPDATE students SET password='$newPassword' WHERE email='$student_email'";
                        if ($conn->query($update_password_query) === TRUE) {
                            $error_message = "Password updated successfully.";
                        } else {
                            $error_message = "Error updating password: " . $conn->error;
                        }
                    }
                }
            }
        } else {
            $error_message = "Student not found.";
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
    <title>Student Update Profile</title>
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

        .container {
            margin-left: 290px;
            margin-top: 20px;
            padding: 20px;
            border: 5px solid black; /* Border with a width of 2px and black color */
        }

        .form-group {
            margin-bottom: 20px;
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
    <a href="student_home.php">Home</a>
    <a href="student_view_courses.php">My Courses</a>
    <a href="student_buy_course.php">Request Tutor</a>
    <a href="student_view_status.php">View Request Status</a>
    <a href="student_update_profile.php"><b><i>Update Profile</b></i></a>
    <!-- Add more options here -->
</div>

<h2>Update Profile</h2>
<div class="container">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="currentName">Current Name:</label>
            <input type="text" class="form-control" id="currentName" name="currentName" value="<?php echo $student_name; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="newName">New Name:</label>
            <input type="text" class="form-control" id="newName" name="newName" value="<?php echo $student_name; ?>">
        </div>
        <div class="form-group">
            <label for="currentEmail">Current Email:</label>
            <input type="email" class="form-control" id="currentEmail" name="currentEmail" value="<?php echo $student_email; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="newEmail">New Email:</label>
            <input type="email" class="form-control" id="newEmail" name="newEmail" value="<?php echo $student_email; ?>">
        </div>
        <button type="submit" class="btn btn-primary" name="updateProfile">Update Profile</button>
    </form>
    <br>
    <h2>Update Password</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="currentPassword">Current Password:</label>
            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
        </div>
        <div class="form-group">
            <label for="newPassword">New Password:</label>
            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
        </div>
        <div class="form-group">
            <label for="confirmNewPassword">Confirm New Password:</label>
            <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword" required>
        </div>
        <button type="submit" class="btn btn-primary" name="updatePassword">Update Password</button>
    </form>
    <br>
    <?php if (!empty($error_message)): ?>
        <?php if ($error_message == "Profile updated successfully." || $error_message == "Password updated successfully."): ?>
            <div class="success-message">
                <?php echo $error_message; ?>
            </div>
        <?php else: ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</center>
</body>
</html>
