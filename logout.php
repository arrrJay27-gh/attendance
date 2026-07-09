<?php
// Destroys the current session and returns the user to the login page.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

// Remove the session cookie itself
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header('Location: login.php');
exit();