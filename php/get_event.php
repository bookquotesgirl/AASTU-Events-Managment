<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$eventId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$eventId || $eventId <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Valid Event ID is required"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $allEvents = $conn->query("SELECT id, name FROM events LIMIT 10");
        $eventsList = [];
        while ($row = $allEvents->fetch_assoc()) {
            $eventsList[] = $row;
        }

        http_response_code(404);
        echo json_encode([
            "error" => "Event not found",
            "available_events" => $eventsList
        ]);
        exit;
    }

    $event = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $event]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
