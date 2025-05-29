<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$response = [
    'success' => false,
    'data' => [],
    'error' => ''
];



try {
    // Modified query without status filter
    $query = "SELECT 
                id, 
                name, 
                start_date,
                start_time,
                end_date,
                end_time,
                venue,
                event_type,
                capacity
              FROM events 
              ORDER BY start_date ASC, start_time ASC";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Execute the query
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    // Get result
    $result = $stmt->get_result();
    
    // Fetch all events
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'start_date' => $row['start_date'],
            'start_time' => $row['start_time'],
            'end_date' => $row['end_date'],
            'end_time' => $row['end_time'],
            'venue' => $row['venue'],
            'event_type' => $row['event_type'],
            'capacity' => $row['capacity']
        ];
    }
    
    // Close statement
    $stmt->close();
    
    // Prepare successful response
    $response['success'] = true;
    $response['data'] = $events;
    
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
} finally {
    // Close connection
    $conn->close();
}

// Return JSON response
echo json_encode($response);
?>