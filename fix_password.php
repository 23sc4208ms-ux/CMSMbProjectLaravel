<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'students');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$password = 'StudentPass123!';
$hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

echo "Original password: $password\n";
echo "Generated hash: $hashedPassword\n\n";

// Test if the hash verifies
$verifyTest = password_verify($password, $hashedPassword);
echo "Verification test: " . ($verifyTest ? "✓ VALID" : "✗ INVALID") . "\n\n";

// Update user with new hash
$sql = "UPDATE users SET password = ? WHERE email = 'student@example.com'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $hashedPassword);

if ($stmt->execute()) {
    echo "✓ User password updated successfully!\n";

    // Verify the update
    $result = $conn->query("SELECT email, password FROM users WHERE email = 'student@example.com'");
    $user = $result->fetch_assoc();

    echo "\nVerifying stored password:\n";
    echo "Password Hash: " . substr($user['password'], 0, 50) . "...\n";
    $isFinalValid = password_verify($password, $user['password']);
    echo "Verification: " . ($isFinalValid ? "✓ VALID" : "✗ INVALID") . "\n";
} else {
    echo "✗ Error updating password: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
