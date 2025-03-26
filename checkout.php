<?php
session_start();
require_once 'db_connect.php';  // Ensure the database connection is working

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Save order to database
    $user_id = 1; // This should be dynamically fetched from logged-in user
    $total_price = 0;

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price) VALUES (:user_id, :total_price)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->execute();

        $order_id = $conn->lastInsertId();

        // Insert each item into order_items table
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, product_price, quantity) VALUES (:order_id, :product_name, :product_price, :quantity)");
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':product_name', $item['name']);
            $stmt->bindParam(':product_price', $item['price']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->execute();
        }

        // Clear the cart
        unset($_SESSION['cart']);

        // Redirect to confirmation page
        header("Location: confirmation.php");
    }
}
?>