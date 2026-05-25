<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Starting user insertion...\n";

try {
    $conn = new mysqli('127.0.0.1', 'root', '', 'students');

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    echo "Connected to database successfully\n";

    // Argon2id hash: password "StudentPass123!"
    $hash = '$argon2id$v=19$m=65536,t=4,p=1$bFJFdUI4c2ZmZHh2N2JFaQ$rL7NdEHpHvJtZiQvETDPC4vY3giuxsTp7m27G72joDU';

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at, updated_at)
                            VALUES (?, ?, ?, NOW(), NOW())
                            ON DUPLICATE KEY UPDATE
                            password = VALUES(password), updated_at = NOW()");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $name = 'Student User';
    $email = 'student@example.com';

    $stmt->bind_param('sss', $name, $email, $hash);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    echo "✓ User inserted/updated successfully!\n";

    // Verify
    $result = $conn->query("SELECT id, name, email, SUBSTR(password, 1, 20) as hash_prefix
                           FROM users WHERE email = 'student@example.com'");

    if ($row = $result->fetch_assoc()) {
        echo "  ID: " . $row['id'] . "\n";
        echo "  Name: " . $row['name'] . "\n";
        echo "  Email: " . $row['email'] . "\n";
        echo "  Hash Prefix: " . $row['hash_prefix'] . "...\n";
    }

    $conn->close();
    echo "\nDone!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
