<?php
declare(strict_types=1);

// Authenticate a user using their submitted credentials.
function loginUser(PDO $pdo, array $data): array
{
    // Retrieve the submitted email and password.
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';

    // Ensure both fields have been completed.
    if ($email === '' || $password === '') {
        return [
            'success' => false,
            'message' => 'Please enter your email and password.'
        ];
    }

    // Find the account matching the submitted email.
    $statement = $pdo->prepare(
        'SELECT user_id, password_hash, role
         FROM users
         WHERE email = ?'
    );

    $statement->execute([$email]);

    $user = $statement->fetch();

    // Reject missing accounts or incorrect passwords.
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return [
            'success' => false,
            'message' => 'Incorrect email or password.'
        ];
    }

    // Start an authenticated session.
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role'] = $user['role'];

    // Return the authenticated user's role.
    return [
        'success' => true,
        'message' => 'Login successful!',
        'role' => $user['role']
    ];
}
