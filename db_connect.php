<?php
// Database connection settings
$servername = "localhost"; // Default for XAMPP
$username = "root";        // Default for XAMPP
$password = "";            // Default for XAMPP (no password)
$dbname = "efdm";          // Your actual database name

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optionally you can echo success message (only for testing)
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>