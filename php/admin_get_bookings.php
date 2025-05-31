<?php
// Enable extensive error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'db_connect.php';

$response = [
    'success' => false,
    'data' => [],
    'debug' => [],
    'error' => ''
];

try {
    // Verify connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Debug: Check if tables exist
    $tables = ['bookings', 'users', 'tickets', 'events'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        $response['debug']['tables'][$table] = $result->num_rows > 0;
    }

    // Debug: Get row counts
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $response['debug']['row_counts'][$table] = $result->fetch_assoc()['count'];
    }

    // Get parameters with validation
    $eventId = isset($_GET['event']) && $_GET['event'] !== 'all' ? (int)$_GET['event'] : null;
    $status = isset($_GET['status']) && $_GET['status'] !== 'all' ? $conn->real_escape_string($_GET['status']) : null;
    $dateFrom = isset($_GET['dateFrom']) ? $conn->real_escape_string($_GET['dateFrom']) : null;
    $dateTo = isset($_GET['dateTo']) ? $conn->real_escape_string($_GET['dateTo']) : null;

    // Base query with LEFT JOINs for safety
    $query = "SELECT 
                b.id, 
                b.booking_date, 
                b.status,
                t.name as ticket_name,
                t.price as ticket_price,
                u.username as attendee_name, 
                u.email as attendee_email,
                u.phone as attendee_phone,
                e.name as event_name
              FROM bookings b
              LEFT JOIN users u ON b.user_id = u.id
              LEFT JOIN tickets t ON b.ticket_id = t.id
              LEFT JOIN events e ON b.event_id = e.id
              WHERE 1=1";

    $conditions = [];
    $params = [];
    $types = '';

    if ($eventId) {
        $conditions[] = "b.event_id = ?";
        $params[] = $eventId;
        $types .= 'i';
    }
    if ($status) {
        $conditions[] = "b.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    if ($dateFrom) {
        $conditions[] = "DATE(b.booking_date) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    if ($dateTo) {
        $conditions[] = "DATE(b.booking_date) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }

    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY b.booking_date DESC";

    $response['debug']['final_query'] = $query;
    $response['debug']['params'] = $params;

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);

    $response['success'] = true;
    $response['data'] = $bookings;
    $response['debug']['num_rows'] = count($bookings);

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}

echo json_encode($response);
?>