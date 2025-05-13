<?php
// Enable mysqli exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: application/json');

try {
    // Decode JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON input."]);
        exit;
    }

    // Validate required field: user_email
    $user_email = trim($data['user_email'] ?? '');
    if (empty($user_email)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing required field: user_email."]);
        exit;
    }

    // Database credentials
    $host = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbName = 'event_management';

    // Connect to database
    $conn = new mysqli($host, $dbUser, $dbPass, $dbName);
    $conn->set_charset('utf8mb4');

    // Verify user exists
    $uStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $uStmt->bind_param('s', $user_email);
    $uStmt->execute();
    $uStmt->store_result();
    if ($uStmt->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "User not found."]);
        exit;
    }
    $uStmt->bind_result($user_id);
    $uStmt->fetch();
    $uStmt->close();

    // Fetch bookings for user
    $q = "SELECT b.id AS booking_id, e.name AS event_name, b.tickets, e.date, e.location
          FROM bookings b
          JOIN events e ON b.event_id = e.id
          WHERE b.user_id = ?";
    $bStmt = $conn->prepare($q);
    $bStmt->bind_param('i', $user_id);
    $bStmt->execute();
    $result = $bStmt->get_result();

    if ($result->num_rows === 0) {
        // No bookings
        echo json_encode(["success" => true, "bookings" => [], "message" => "No bookings found for this user."]);
        exit;
    }

    // Collect bookings
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    // Return bookings
    echo json_encode(["success" => true, "bookings" => $bookings]);

    $bStmt->close();
    $conn->close();

} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error. Please try again later."]);
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error. Please try again later."]);
}
?>