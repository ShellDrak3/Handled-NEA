<?php
declare(strict_types=1);

// Start the existing session.
session_start();

// Clear all session variables.
$_SESSION = [];

// Remove the session cookie if cookies are enabled.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session.
session_destroy();

// Redirect the user to the login page.
header('Location: ../../public/index.php');
exit;
