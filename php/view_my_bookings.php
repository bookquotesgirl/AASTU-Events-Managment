<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_management";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set response header
header('Content-Type: application/json');

// Get parameters safely
$email = isset($_GET['email']) ? $conn->real_escape_string($_GET['email']) : '';
$role = $_GET['role'] ?? '';

$bookings = [];

// Determine SQL query based on role
if ($role === 'admin') {
    $sql = "SELECT * FROM bookings";
} else if ($role === 'user' && !empty($email)) {
    $sql = "SELECT * FROM bookings WHERE user_email='$email'";
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

// Execute the query
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Return the data
echo json_encode([
    "success" => true,
    "data" => $bookings
]);

$conn->close();
?>
