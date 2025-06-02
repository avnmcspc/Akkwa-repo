<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set a logout success message in a temporary session
session_start();
$_SESSION['logout_message'] = "You have been successfully logged out!";

// Redirect to the login page
header("Location: login-form.php");
exit();
