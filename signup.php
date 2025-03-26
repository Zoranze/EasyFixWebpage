<?php
// Display all errors for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection configuration
$host = 'localhost';
$db = 'efdm';
$user = 'root';  // Replace with your database username
$pass = '';      // Replace with your database password

try {
    // Establish a connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debugging: Print the contents of $_POST to see what data is coming in
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Collect form inputs
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;  // Change from 'name' to 'username'
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

    // Input validation
    $errors = [];

    // Ensure that username, email, and password are not empty
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, proceed to insert data into the database
    if (empty($errors)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Prepare an SQL statement to insert user details
            $sql = "INSERT INTO user (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters to the statement
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);  // Change 'name' to 'username'
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

            // Execute the statement
            $stmt->execute();

            // Success message or redirection
            echo "<p style='color: green;'>Account created successfully. You can now <a href='login.html'>log in</a>.</p>";

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                // Duplicate email error (assuming email is unique)
                echo "<p style='color: red;'>This email address is already registered.</p>";
            } else {
                // General error
                echo "Error: " . $e->getMessage();
            }
        }
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>
