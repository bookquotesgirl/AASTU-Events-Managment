<?php

header('Content-Type: application/json');


ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/php_errors.log');

// Start session after headers
session_start();

require_once 'db_connect.php';

// Buffer any potential output
ob_start();

try {
    // Validate session
    if (!isset($_SESSION['userId'])) {
        throw new Exception("Please log in to view bookings.");
    }

    $user_id = $_SESSION['userId'];

    // Get user's bookings
    $query = "SELECT 
                b.id, b.ticket_id, b.event_id, b.booking_date, b.status,
                e.name AS event_name, e.description, e.image, e.venue,
                e.start_date, e.start_time, e.end_time,
                t.name AS ticket_name, t.price
              FROM bookings b
              JOIN events e ON b.event_id = e.id
              LEFT JOIN tickets t ON b.ticket_id = t.id
              WHERE b.user_id = ?
              ORDER BY b.booking_date DESC";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Query failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Clear any buffered output
    ob_end_clean();

    echo json_encode([
        'status' => 'success',
        'bookings' => $bookings
    ]);
    exit;

} catch (Exception $e) {
    // Clean any buffered output
    ob_end_clean();
    
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

$conn->close();
?>