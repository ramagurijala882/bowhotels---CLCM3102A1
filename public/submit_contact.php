<?php
// Secure headers
header("Content-Type: text/html; charset=UTF-8");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header("Permissions-Policy: interest-cohort=()");

// Disable error display in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Load database credentials from environment variables
$servername = getenv('DB_HOST') ?: 'g4lab8.czptxhzjxjrt.us-east-1.rds.amazonaws.com';
$username = getenv('DB_USER') ?: 'admin';
$password = getenv('DB_PASS') ?: 'Melburn3$';
$dbname = getenv('DB_NAME') ?: 'g4lab8';

// Global exception handler
function customExceptionHandler($exception) {
    error_log("Exception: " . $exception->getMessage()); // Log error
    http_response_code(500); // Send a proper HTTP error status
    echo json_encode(["error" => "An unexpected error occurred. Please try again later."]);
}
set_exception_handler("customExceptionHandler");

try {
    // Establish database connection using MySQLi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get and sanitize form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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

    // Success response
    echo json_encode(["success" => "New record created successfully"]);

    // Close connections
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    throw $e; // Handled by the global exception handler
}
?>
