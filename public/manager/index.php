<?php
declare(strict_types=1);

// Protect the manager portal.
require_once __DIR__ . '/../../src/Authentication/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard | Handled</title>
    <link rel="stylesheet" href="../assets/css/shared/colours.css">
    <link rel="stylesheet" href="../assets/css/shared/style.css">
</head>
<body>

    <h1>Manager Dashboard</h1>
    <p>Welcome to Handled.</p>

    <a href="../../src/Authentication/logout.php">Logout</a>

</body>
</html>
