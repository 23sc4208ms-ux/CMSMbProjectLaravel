<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'students');
$hash = '$argon2id$v=19$m=65536,t=4,p=1$bFJFdUI4c2ZmZHh2N2JFaQ$rL7NdEHpHvJtZiQvETDPC4vY3giuxsTp7m27G72joDU';
mysqli_query($conn, "INSERT INTO users (name, email, password, created_at, updated_at) VALUES ('Student User', 'student@example.com', '$hash', NOW(), NOW()) ON DUPLICATE KEY UPDATE password = VALUES(password)");
$result = mysqli_query($conn, "SELECT * FROM users WHERE email='student@example.com'");
if ($row = mysqli_fetch_assoc($result)) file_put_contents('verify_user.txt', "USER CREATED: ID=" . $row['id'] . ", Name=" . $row['name'] . ", Email=" . $row['email']);
mysqli_close($conn);
