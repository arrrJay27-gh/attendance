<?php

// Prevent browser caching of protected pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If there's no authenticated user in the session, send them to the login page.
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}