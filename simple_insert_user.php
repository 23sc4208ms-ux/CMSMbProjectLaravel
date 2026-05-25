<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'students');

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Full Argon2id hash that was tested and verified earlier
$argon2id_hash = '$argon2id$v=19$m=65536,t=4,p=1$bFJFdUI4c2ZmZHh2N2JFaQ$rL7NdEHpHvJtZiQvETDPC4vY3giuxsTp7m27G72joDU';

$sql = "INSERT INTO users (name, email, password, created_at, updated_at)
        VALUES ('Student User', 'student@example.com', '$argon2id_hash', NOW(), NOW())
        ON DUPLICATE KEY UPDATE
        password = VALUES(password),
        updated_at = NOW()";

if (mysqli_query($conn, $sql)) {
    echo "✓ User inserted/updated successfully!\n";

    // Verify
    $result = mysqli_query($conn, "SELECT id, name, email, SUBSTR(password, 1, 20) as hash_prefix FROM users WHERE email = 'student@example.com'");
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        echo "  ID: " . $user['id'] . "\n";
        echo "  Name: " . $user['name'] . "\n";
        echo "  Email: " . $user['email'] . "\n";
        echo "  Password Hash: " . $user['hash_prefix'] . "...\n";
    }
} else {
    echo "Error: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
