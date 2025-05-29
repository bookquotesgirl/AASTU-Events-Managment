<?php
require_once 'db_connect.php';

$sql = "SELECT e.id, e.name, e.start_date, e.start_time, e.category, e.event_type, e.status, 
               e.capacity, COUNT(b.id) as registrations, e.image 
        FROM events e
        LEFT JOIN bookings b ON e.id = b.event_id
        GROUP BY e.id, e.name, e.start_date, e.start_time, e.category, e.event_type, e.status, e.capacity, e.image
        ORDER BY e.start_date DESC";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    exit;
}

$events = [];

while ($row = $result->fetch_assoc()) {
    // Check if image is set and not empty
    if (!empty($row['image'])) {
        // Prepend the folder path to image filename
        $row['image'] = 'php/uploads/' . $row['image'];
    } else {
        // Provide a default image path if no image
        $row['image'] = 'php/uploads/default.png';
    }
    $events[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($events);
$conn->close();
