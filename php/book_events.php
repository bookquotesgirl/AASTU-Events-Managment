<?php
// Enable error reporting for mysqli exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Set JSON header
header('Content-Type: application/json');

try {
    // Parse JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON input."]);
        exit;
    }

    // Validate required fields
    $ticket_type = trim($data['ticket_type'] ?? '');
    $event_name = trim($data['event_name'] ?? '');
    $user_name  = trim($data['user_name']  ?? '');
    $user_email = trim($data['user_email'] ?? '');

    if (empty($ticket_type)  empty($event_name)  empty($user_name) || empty($user_email)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing required fields."]);
        exit;
    }

    // Database credentials
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "event_management";

    // Connect to database
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');

    // Check if user exists
    $uStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $uStmt->bind_param("s", $user_email);
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

    // Check if event exists
    $eStmt = $conn->prepare("SELECT id, available_tickets FROM events WHERE name = ?");
    $eStmt->bind_param("s", $event_name);
    $eStmt->execute();
    $eStmt->store_result();
    if ($eStmt->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Event not found."]);
        exit;
    }
    $eStmt->bind_result($event_id, $available);
    $eStmt->fetch();
    $eStmt->close();

    // Check ticket availability
    if ((int)$ticket_type > (int)$available) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Not enough tickets available."]);
        exit;
    }

    // Insert booking
    $bStmt = $conn->prepare(
        "INSERT INTO bookings (event_id, user_id, tickets) VALUES (?, ?, ?)"
    );
    $bStmt->bind_param("iii", $event_id, $user_id, $ticket_type);
    $bStmt->execute();

    // Update available tickets
    $u2Stmt = $conn->prepare(
        "UPDATE events SET available_tickets = available_tickets - ? WHERE id = ?"
    );
    $u2Stmt->bind_param("ii", $ticket_type, $event_id);
    $u2Stmt->execute();

    // Success
    echo json_encode(["success" => true, "message" => "Booking successful."]);

    // Close statements and connection
    $bStmt->close();
    $u2Stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    // Log error (in production, write to file or monitoring)
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error. Please try again later."]);
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error. Please try again later."]);
}
?>