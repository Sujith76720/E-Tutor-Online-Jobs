<?php
// Start a session
session_start();

// Establish a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = ""; // If you have set a password, provide it here
$database = "software"; // Replace "your_database_name" with the actual name of your database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the role is valid
    if ($role == "student") {
        $table = "students";
    } elseif ($role == "tutor") {
        $table = "tutors";
    } elseif ($role == "admin") {
        $table = "admins";
    } else {
        $error_message = "Invalid role.";
    }

    if (empty($error_message)) {
        // Check if the user exists in the database
        $check_query = "SELECT * FROM $table WHERE email='$email'";
        $result = $conn->query($check_query);

        if ($result->num_rows > 0) {
            // User found, check password
            $row = $result->fetch_assoc();
            if ($row["password"] == $password) {
                // Password is correct, store role and email in session
                $_SESSION['role'] = $role;
                $_SESSION['email'] = $email;

                // Redirect to the appropriate home page
                if ($role == "student") {
                    header("Location: student_home.php");
                } elseif ($role == "tutor") {
                    header("Location: tutor_home.php");
                } elseif ($role == "admin") {
                    header("Location: admin_home.php");
                }
                exit(); // Make sure to exit after redirection
            } else {
                // Password is incorrect
                $error_message = "Invalid Password";
            }
        } else {
            // User not found
            $error_message = "User not registered";
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
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            width: 400px;
            text-align: center;
        }

        select,
        input,
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <form action="login_process.php" method="POST">
            <label for="role">Select Role</label><br>
            <select id="role" name="role">
                <option value="student">Student</option>
                <option value="tutor">Tutor</option>
                <option value="admin">Admin</option>
            </select><br><br>
            <input type="email" name="email" placeholder="Your Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.html">Sign Up</a></p>
        <?php if (!empty($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
