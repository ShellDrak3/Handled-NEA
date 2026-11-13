<?php
declare(strict_types=1);

// Start the session.
session_start();

// Ensure the user is authenticated.
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: ../index.php');
    exit;
}

// Restrict access to the customer portal.
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../manager/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portal | Handled</title>
    <link rel="stylesheet" href="../assets/css/shared/colours.css">
    <link rel="stylesheet" href="../assets/css/shared/style.css">
</head>
<body>

    <h1>Customer Portal</h1>
    <p>Welcome to Handled.</p>

    <a href="../../src/Authentication/logout.php">Logout</a>

</body>
</html>
