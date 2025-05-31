<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    $query = "SELECT * FROM events 
              WHERE start_date >= CURDATE() 
                 OR (end_date >= CURDATE() AND end_date != '0000-00-00')
              ORDER BY start_date ASC, start_time ASC";

    $result = $conn->query($query);

    if (!$result) {
        throw new Exception($conn->error);
    }

    $events = [];

    while ($row = $result->fetch_assoc()) {
        // Format dates
        $row['start_date'] = date('Y-m-d', strtotime($row['start_date']));
        $row['end_date'] = (!empty($row['end_date']) && $row['end_date'] != '0000-00-00')
            ? date('Y-m-d', strtotime($row['end_date']))
            : null;

        // Fix image path
        if (!empty($row['image'])) {
            $row['image'] = str_replace('\\', '/', $row['image']);
            if (strpos($row['image'], 'uploads/') === 0) {
                $row['image'] = 'php/' . $row['image'];
            }
        } else {
            $row['image'] = 'images/default.jpg';
        }

        $events[] = $row;
    }

    echo json_encode($events);

} catch (Exception $e) {
    error_log("Fetch error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Could not fetch events.',
        'details' => $e->getMessage()
    ]);
}

$conn->close();
?>
