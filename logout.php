<?php
session_start(); // Start the session

// Unset all of the session variables
$_SESSION = array();

// If it's desired to destroy the session cookie, then also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Redirect to login page
header('Location: login/login.php');
exit();
?>
