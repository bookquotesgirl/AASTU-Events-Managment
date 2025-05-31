<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'No event ID provided.']);
    exit;
}

$eventId = intval($_GET['id']);

try {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        echo json_encode(['error' => 'Event not found.']);
        exit;
    }

    $event = $result->fetch_assoc();

    // Format dates
    $event['start_date'] = date('Y-m-d', strtotime($event['start_date']));
    $event['end_date'] = (!empty($event['end_date']) && $event['end_date'] != '0000-00-00')
        ? date('Y-m-d', strtotime($event['end_date']))
        : null;

    // Fix image path
    if (!empty($event['image'])) {
    $event['image'] = str_replace('\\', '/', $event['image']);
    if (strpos($event['image'], 'uploads/') === 0) {
        $event['image'] = 'php/' . $event['image'];
    }
} else {
    $event['image'] = 'images/default.jpg';
}

    // You might also want to fetch tickets here if you're using them
    $ticketQuery = $conn->prepare("SELECT * FROM tickets WHERE event_id = ?");
    $ticketQuery->bind_param("i", $eventId);
    $ticketQuery->execute();
    $ticketResult = $ticketQuery->get_result();

    $tickets = [];
    while ($ticket = $ticketResult->fetch_assoc()) {
        $tickets[] = $ticket;
    }

    $event['tickets'] = $tickets;

    echo json_encode($event);

} catch (Exception $e) {
    error_log("Fetch error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Could not fetch event.',
        'details' => $e->getMessage()
    ]);
}

$conn->close();
?>
