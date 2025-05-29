<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // First delete related bookings
    $deleteBookings = "DELETE FROM bookings WHERE event_id = $id";
    mysqli_query($conn, $deleteBookings);

    // Then delete the event
    $deleteEvent = "DELETE FROM events WHERE id = $id";
    mysqli_query($conn, $deleteEvent);
}

header('Location: /IPII/AASTU-Events-Managment/admin.php?msg=deleted');
exit;
