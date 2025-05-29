<?php
require_once 'db_connect.php';

// Admin user details
$username = "admin";
$password = "admin123";
$role = "admin";
$email = "admin@example.com";
$phone = "0912345678";  

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO users (username, password, role, email, phone) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $hashed_password, $role, $email, $phone);

// Execute and check result
if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Cleanup
$stmt->close();
$conn->close();
?>
