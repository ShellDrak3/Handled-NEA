<?php
declare(strict_types=1);

// Start the session if one has not already been started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is authenticated.
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: ../index.php');
    exit;
}

// Restrict access to the manager portal.
if ($_SESSION['role'] !== 'manager') {
    header('Location: ../customer/');
    exit;
}
