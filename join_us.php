<?php
// Database configuration
$host = 'localhost'; // Update with your host if different
$dbname = 'efdm'; // The name of your database
$username = 'root'; // Your database username
$password = ''; // Your database password

// Connect to the database using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $service_type = trim($_POST['service_type']);
    $region = trim($_POST['region']);
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $payment_method = $_POST['payment_method'];
    $message = trim($_POST['message']);

    // Validate the inputs
    if (empty($name) || empty($email) || empty($service_type) || empty($region) || !$age || empty($payment_method)) {
        die("Please fill all the required fields correctly.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    // Insert the data into the `service_providers` table
    $sql = "INSERT INTO service_providers (name, email, service_type, region, age, payment_method, message) 
            VALUES (:name, :email, :service_type, :region, :age, :payment_method, :message)";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind the parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':service_type', $service_type);
    $stmt->bindParam(':region', $region);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':message', $message);

    // Execute the query
    if ($stmt->execute()) {
        echo "Thank you for applying to join us! We will contact you soon.";
    } else {
        echo "There was an error submitting your application. Please try again.";
    }
}
?>