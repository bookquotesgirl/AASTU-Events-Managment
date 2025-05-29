<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    // Validate event ID parameter
    $eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
    
    if (!$eventId) {
        http_response_code(400);
        die(json_encode(['error' => 'Event ID is required']));
    }

    // First, check if the event exists
    $stmt = $conn->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->bind_param('i', $eventId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        die(json_encode(['error' => 'Event not found']));
    }

    // Get tickets without availability calculation
    $stmt = $conn->prepare("
        SELECT 
            id, 
            name, 
            description, 
            price, 
            quantity
        FROM tickets
        WHERE event_id = ?
    ");
    
    $stmt->bind_param('i', $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    $tickets = [];
    while ($row = $result->fetch_assoc()) {
        $tickets[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'description' => $row['description'] ?? '',
            'price' => (float)$row['price'],
            'quantity' => (int)$row['quantity'],
            'status' => ($row['quantity'] == 0) ? 'unlimited' : 'available'
        ];
    }

    if (empty($tickets)) {
        http_response_code(404);
        die(json_encode(['error' => 'No tickets available for this event']));
    }

    echo json_encode([
        'success' => true,
        'tickets' => $tickets
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}