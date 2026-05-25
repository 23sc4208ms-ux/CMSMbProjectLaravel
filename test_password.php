<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'students');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$result = $conn->query("SELECT id, name, email, password FROM users WHERE email = 'student@example.com'");
$user = $result->fetch_assoc();

if ($user) {
    echo "✓ User found in database\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Name: " . $user['name'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Password Hash: " . substr($user['password'], 0, 50) . "...\n\n";

    // Test password verification
    $testPassword = 'StudentPass123!';
    $isValid = password_verify($testPassword, $user['password']);

    echo "Testing password: '$testPassword'\n";
    echo "Password verification: " . ($isValid ? "✓ VALID" : "✗ INVALID") . "\n";
} else {
    echo "✗ User not found in database\n";
}

$conn->close();
?>
