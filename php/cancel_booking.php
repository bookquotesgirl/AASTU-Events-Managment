<?php
header('Content-Type: application/json');
session_start();
require_once 'db_connect.php';

try {
    // Validate session
    if (!isset($_SESSION['userId'])) {
        throw new Exception("Please log in to cancel bookings.");
    }

    // Validate request method
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        throw new Exception("Method not allowed");
    }

    // Validate booking ID
    if (!isset($_POST['booking_id']) || !ctype_digit($_POST['booking_id'])) {
        throw new Exception("Invalid booking specified.");
    }

    $booking_id = intval($_POST['booking_id']);
    $user_id = $_SESSION['userId'];

    // Verify booking belongs to user and isn't already cancelled
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ? AND status != 'cancelled'");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Booking not found, doesn't belong to you, or is already cancelled.");
    }
    $stmt->close();

    // Cancel booking (update status)
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to cancel booking: " . $conn->error);
    }
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => 'Booking cancelled successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>