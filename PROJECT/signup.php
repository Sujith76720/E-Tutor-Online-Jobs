<?php
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if student/tutor with the same email exists
    $check_query = "SELECT * FROM students WHERE email='$email'";
    if ($role == 'tutor') {
        $check_query = "SELECT * FROM tutors WHERE email='$email'";
    }
    
    $result = $conn->query($check_query);
    
    if ($result->num_rows > 0) {
        // Student/tutor with the same email already exists
        echo "Already registered. Please login.";
    } else {
        // Insert student/tutor details into the database
        if ($role == 'student') {
            $insert_query = "INSERT INTO students (name, email, password) VALUES ('$name', '$email', '$password')";
        } else if ($role == 'tutor') {
            $insert_query = "INSERT INTO tutors (name, email, password) VALUES ('$name', '$email', '$password')";
        }

        if ($conn->query($insert_query) === TRUE) {
            echo "Successfully registered. Please login.";
            // Redirect to login page or do any other necessary action
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
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
    <title>Signup</title>
</head>
<body>
    <!-- Your signup form code here -->

    <!-- Login button -->
    <form action="login.html">
        <button type="submit">Login</button>
    </form>
</body>
</html>
