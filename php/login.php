<?php
// login.php

session_start(); // START SESSION

header("Content-Type: application/json");
require_once 'db_connect.php';

// 2. Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Username and password are required."]);
    exit();
}

// 3. Query the user
$stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
    exit();
}

// 4. Set session variables
$_SESSION['userId'] = $user['id'];
$_SESSION['role'] = $user['role'];

// 5. Successful login response
echo json_encode([
    "success" => true,
    "message" => "Login successful.",
    "userId" => $user['id'],
    "role" => $user['role']
]);

$conn->close();
?>
