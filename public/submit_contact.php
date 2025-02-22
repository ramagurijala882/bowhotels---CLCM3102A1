<?php
// Secure headers
header("Content-Type: text/html; charset=UTF-8");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Disable error display in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Database credentials (Use environment variables in production)
$servername = "g4lab8.czptxhzjxjrt.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "Melburn3$";
$dbname = "g4lab8";

try {
    // Establish database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get and sanitize form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (!$name || !$phone || !$email || !$subject || !$message) {
        throw new Exception("All fields are required.");
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO contacts (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Statement preparation failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $phone, $email, $subject, $message);

    // Execute the query and check for success
    if (!$stmt->execute()) {
        throw new Exception("Error inserting data: " . $stmt->error);
    }

    echo "New record created successfully";

    // Close connections
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log($e->getMessage()); // Log errors for debugging
    echo "An error occurred. Please try again later."; // Display user-friendly message
}
?>
