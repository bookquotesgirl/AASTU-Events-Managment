<?php
// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/php_errors.log');

// Headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();
require_once 'db_connect.php';

try {
    // Validate session
    if (!isset($_SESSION['userId'])) {
        throw new Exception("Please log in to book events.");
    }

    // Validate method
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        throw new Exception("Method not allowed");
    }

    // Validate event ID
    if (!isset($_POST['event_id']) || !ctype_digit($_POST['event_id'])) {
        throw new Exception("Invalid event specified.");
    }

    $event_id = intval($_POST['event_id']);
    $user_id = $_SESSION['userId'];

    // Verify event exists
    $stmt = $conn->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("The selected event does not exist.");
    }
    $stmt->close();

    // Check for duplicate booking
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE event_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $event_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception("You've already booked this event.");
    }
    $stmt->close();

    // Get first available ticket
    $stmt = $conn->prepare("SELECT id FROM tickets WHERE event_id = ? AND quantity > 0 LIMIT 1");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No available tickets for this event.");
    }

    $ticket = $result->fetch_assoc();
    $ticket_id = $ticket['id'];
    $stmt->close();

    // Create booking with ticket_id
    $stmt = $conn->prepare("INSERT INTO bookings (event_id, user_id, ticket_id, booking_date, status) VALUES (?, ?, ?, NOW(), 'confirmed')");
    $stmt->bind_param("iii", $event_id, $user_id, $ticket_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to create booking: " . $conn->error);
    }

    // Update ticket quantity
    $conn->query("UPDATE tickets SET quantity = quantity - 1 WHERE id = $ticket_id");

    echo json_encode([
        'status' => 'success', 
        'message' => 'Booking confirmed!',
        'booking_id' => $stmt->insert_id,
        'ticket_id' => $ticket_id
    ]);
    $stmt->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();