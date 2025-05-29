<?php
session_start();
require_once 'db_connect.php'; // DB connection file
// Debug ticket data
error_log("TICKET DATA RECEIVED:");
error_log("Ticket Names: " . print_r($_POST['ticket_name'] ?? 'Not set', true));
error_log("Ticket Prices: " . print_r($_POST['ticket_price'] ?? 'Not set', true));
error_log("Ticket Quantities: " . print_r($_POST['ticket_quantity'] ?? 'Not set', true));
// Get the user ID from session
if (!isset($_SESSION['userId'])) {
    die("Unauthorized access.");
}
$created_by = $_SESSION['userId'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Sanitize and get inputs
        $event_name = trim($_POST['event_name']);
        $short_description = trim($_POST['short_description']);
        $start_date = $_POST['start_date'];
        $start_time = $_POST['start_time'];
        $end_date = $_POST['end_date'] ?? null;
        $end_time = $_POST['end_time'] ?? null;
        $event_type = $_POST['event_type'];
        $location = $event_type === 'physical' ? trim($_POST['location']) : null;
        $online_url = $event_type === 'online' ? trim($_POST['online_url']) : null;
        $registration_type = $_POST['registration_type'];
        $capacity = $_POST['capacity'] ?? null;
        $registration_deadline = $_POST['registration_deadline'] ?? null;
        $send_notifications = isset($_POST['send_notifications']) ? 1 : 0;
        $featured_event = isset($_POST['featured_event']) ? 1 : 0;
        $require_approval = isset($_POST['require_approval']) ? 1 : 0;

        // Optional: collect rich text content if using an editor
        $full_description = $_POST['full_description'] ?? '';

        // File upload
        $image_path = '';
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
            $uploadDir = 'uploads/';
            $fileName = time() . '_' . basename($_FILES['event_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $targetPath)) {
                $image_path = $targetPath;
            } else {
                throw new Exception("Image upload failed.");
            }
        }

        // Insert event
        $stmt = $conn->prepare("INSERT INTO events 
            (name, description, start_date, start_time, end_date, end_time, venue, category, event_type, capacity, image, status, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, '', ?, ?, ?, 'active', ?, NOW())");

        $stmt->execute([
            $event_name,
            $short_description,
            $start_date,
            $start_time,
            $end_date,
            $end_time,
            $event_type === 'physical' ? $location : $online_url,
            $event_type,
            $capacity,
            $image_path,
            $created_by
        ]);

        $event_id = $conn->insert_id;

        // Handle tickets (modified version)
if (isset($_POST['ticket_name']) && is_array($_POST['ticket_name'])) {
    $ticket_names = $_POST['ticket_name'];
    $ticket_prices = $_POST['ticket_price'] ?? array_fill(0, count($ticket_names), 0);
    $ticket_quantities = $_POST['ticket_quantity'] ?? [];
    
    // Validate arrays
    if (count($ticket_names) !== count($ticket_prices) || 
        count($ticket_names) !== count($ticket_quantities)) {
        throw new Exception("Ticket data arrays don't match in size");
    }

    // Process each ticket
    for ($i = 0; $i < count($ticket_names); $i++) {
        if (empty($ticket_names[$i])) continue;
        
        $price = ($_POST['registration_type'] === 'paid') ? floatval($ticket_prices[$i]) : 0;
        $quantity = intval($ticket_quantities[$i]) ?: 0;
        
        $stmt = $conn->prepare("INSERT INTO tickets 
            (event_id, name, price, quantity) 
            VALUES (?, ?, ?, ?)");
            
        if (!$stmt->execute([
            $event_id,
            trim($ticket_names[$i]),
            $price,
            $quantity
        ])) {
            error_log("Ticket insert failed: " . implode(", ", $stmt->errorInfo()));
        }
    }
}

        echo "<script>alert('Event created successfully!'); window.location.href='/IPII/AASTU-Events-Managment/admin.php';</script>";
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    }
}
?>