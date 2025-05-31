<?php
// logout.php
session_start();
// Destroy all session data to log out the user
session_destroy();

// Redirect to homepage (change 'index.php' to your homepage file)
header("Location: /IPII/AASTU-Events-Managment/home.html");
exit();
?>
