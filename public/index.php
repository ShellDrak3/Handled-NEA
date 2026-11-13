<?php
declare(strict_types=1);

// Start or resume the user's session.
session_start();

// Load the database connection and authentication modules.
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Authentication/register.php';
require_once __DIR__ . '/../src/Authentication/login.php';

// Initialise feedback variables.
$message = '';
$message_type = '';

// Only process submitted forms.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Identify the submitted form.
    if ($_POST['action'] === 'register') {

        // Process registration.
        $result = registerUser($pdo, $_POST);

        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'error';

    } elseif ($_POST['action'] === 'login') {

        // Authenticate the submitted credentials.
        $result = loginUser($pdo, $_POST);

        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'error';

        // Redirect authenticated users to their portal.
        if ($result['success']) {

            if ($result['role'] === 'manager') {
                header('Location: manager/');
            } else {
                header('Location: customer/');
            }

            exit;
        }
    }
}
?>
 <!-- End of PHP -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication | Handled</title>

    <link rel="stylesheet" href="assets/css/shared/colours.css">
    <link rel="stylesheet" href="assets/css/shared/style.css">
</head>
<body class="login-page">

    <main class="login-container">

        <div class="brand">
            <span class="brand-icon">
                <img src="assets/images/handled-favicon.svg" alt="Handled">
            </span>
            <h1>Handled</h1>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?> <!-- End of Success/Error Messages-->

        <!-- Login interface -->
        <section id="login-panel">

            <h2>Welcome back</h2>
            <p class="subtitle">Sign in to your account</p>

            <form method="POST" action="">

                <input type="hidden" name="action" value="login">

                <label for="login-email">Email address</label>
                <input type="email" id="login-email" name="email"
                       placeholder="you@example.com" required>

                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password"
                       placeholder="Enter your password" required>

                <button type="submit">Sign in</button>

            </form>

            <p class="register-link">
                Don't have an account?
                <a href="#" onclick="showRegister(); return false;">Register</a>
            </p>

        </section> <!-- End of Login Interface-->

        <!-- Registration interface -->
        <section id="register-panel" hidden>

            <h2>Create an account</h2>
            <p class="subtitle">Get started with Handled</p>

            <form method="POST" action="">

                <input type="hidden" name="action" value="register">

                <label for="register-name">Full name</label>
                <input type="text" id="register-name" name="full_name"
                       placeholder="Your full name" required>

                <label for="register-email">Email address</label>
                <input type="email" id="register-email" name="email"
                       placeholder="you@example.com" required>

                <label for="register-password">Password</label>
                <input type="password" id="register-password" name="password"
                       placeholder="Create a password" required>

                <label for="confirm-password">Confirm password</label>
                <input type="password" id="confirm-password"
                       name="confirm_password"
                       placeholder="Confirm your password" required>

                <button type="submit">Create account</button>

            </form>

            <p class="register-link">
                Already have an account?
                <a href="#" onclick="showLogin(); return false;">Sign in</a>
            </p> <!-- End of Registeration Interface-->

        </section>

    </main>
    
    <script src="assets/js/shared/auth.js"></script>
</body>
</html>
