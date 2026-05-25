INSERT INTO users (name, email, password, created_at, updated_at) VALUES ('Student User', 'student@example.com', '$argon2id$v=19$m=65536,t=4,p=1$bFJFdUI4c2ZmZHh2N2JFaQ$rL7NdEHpHvJtZiQvETDPC4vY3giuxsTp7m27G72joDU', NOW(), NOW());
SELECT * FROM users WHERE email = 'student@example.com';
