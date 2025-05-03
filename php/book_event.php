<?php
$data = json_decode(file_get_contents('php://input'), true);

$ticket_type = $data['ticket_type'] ?? '';
$event_name = $data['event_name'] ?? '';
$user_name = $data['user_name'] ?? '';
$user_email = $data['user_email'] ?? '';

if (empty($ticket_type) || empty($event_name) || empty($user_name) || empty($user_email)) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

$stmt = $conn->prepare("INSERT INTO bookings (event_name, user_name, user_email, tickets) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $event_name, $user_name, $user_email, $ticket_type);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
