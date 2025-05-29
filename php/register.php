<?php
header("Content-Type: application/json");

require_once 'db_connect.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$password = trim($data['password'] ?? '');

// Basic validation
if (empty($username) || empty($email) || empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Please fill all required fields, including phone number."]);
    exit();
}

// Check if username or email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username or email already exists."]);
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new user with role 'user'
$insertStmt = $conn->prepare("INSERT INTO users (username, password, role, email, phone) VALUES (?, ?, 'user', ?, ?)");
$insertStmt->bind_param("ssss", $username, $hashed_password, $email, $phone);

if ($insertStmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful."]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed. Please try again."]);
}

$insertStmt->close();
$conn->close();
?>
