<?php
// db_connect.php

$host = "localhost";
$dbname = "aastuevent";
$db_user = "root";
$db_pass = "";

// Create connection
$conn = new mysqli($host, $db_user, $db_pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
