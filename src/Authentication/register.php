<?php
declare(strict_types=1);

function registerUser(PDO $pdo, array $data): array
{
    // Retrieve and clean submitted data.
    $name = trim($data['full_name'] ?? '');
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    // Validate required fields.
    if ($name === '' || $email === '' || $password === '') {
        return [
            'success' => false,
            'message' => 'Please complete all required fields.'
        ];
    }

    // Validate email format.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ];
    }

    // Confirm passwords match.
    if ($password !== $confirm_password) {
        return [
            'success' => false,
            'message' => 'Your passwords do not match.'
        ];
    }

    // Check whether the email already exists.
    $statement = $pdo->prepare(
        'SELECT user_id FROM users WHERE email = ?'
    );

    $statement->execute([$email]);

    if ($statement->fetch()) {
        return [
            'success' => false,
            'message' => 'An account with this email already exists.'
        ];
    }

    // Securely hash the password.
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Begin transaction.
        $pdo->beginTransaction();

        // Create user account.
        $statement = $pdo->prepare(
            'INSERT INTO users (email, password_hash, role)
             VALUES (?, ?, ?)'
        );

        $statement->execute([
            $email,
            $password_hash,
            'customer'
        ]);

        // Retrieve the new user ID.
        $user_id = (int) $pdo->lastInsertId();

        // Create customer record.
        $statement = $pdo->prepare(
            'INSERT INTO customers (user_id, full_name, email)
             VALUES (?, ?, ?)'
        );

        $statement->execute([
            $user_id,
            $name,
            $email
        ]);

        // Commit transaction.
        $pdo->commit();

        return [
            'success' => true,
            'message' => 'Your account has been created successfully!'
        ];

    } catch (PDOException $exception) {

        // Undo database changes if something failed.
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        return [
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ];
    }
}
