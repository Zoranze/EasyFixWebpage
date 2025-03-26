<?php
// Start the session
session_start();

// Database connection details (update these if needed)
$host = 'localhost';
$dbname = 'efdm'; // Your database name
$username = 'root'; // Database username (for XAMPP default is 'root')
$password = ''; // Database password (for XAMPP default is empty)

// Error reporting (for debugging, can be disabled in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Establish the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the submitted form data
    $input_email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $input_password = isset($_POST['password']) ? $_POST['password'] : null;

    // Validate that both email and password are entered
    if (!empty($input_email) && !empty($input_password)) {
        // Prepare an SQL statement to find the user by email
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $input_email, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the user record from the database
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and verify the password
        if ($user && password_verify($input_password, $user['password'])) {
            // Set session variables to store user information
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            // Redirect to the home page (or dashboard) after successful login
            header("Location: service.html");
            exit;
        } else {
            // Invalid login details (either user not found or password mismatch)
            $error_message = "Invalid email or password.";
        }
    } else {
        // Display an error if either field is empty
        $error_message = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EasyFix Ltd</title>
    <!-- Add your CSS and Bootstrap links here -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Login to EasyFix Ltd</h2>

        <!-- Display any error messages -->
        <?php if (!empty($error_message)): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Login form -->
        <form action="login.php" method="POST">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                <label for="email">Email</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Your Password" required>
                <label for="password">Password</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3">Login</button>
        </form>

        <!-- Optional link to sign up page -->
        <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</body>
</html>