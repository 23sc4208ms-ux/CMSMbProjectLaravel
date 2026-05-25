-- Insert Student User with Argon2id hashed password
-- Password: StudentPass123! hashed with Argon2id (v=19, m=65536, t=4, p=1)
INSERT INTO users (name, email, password, created_at, updated_at)
VALUES (
  'Student User',
  'student@example.com',
  '$argon2id$v=19$m=65536,t=4,p=1$bFJFdUI4c2ZmZHh2N2JFaQ$rL7NdEHpHvJtZiQvETDPC4vY3giuxsTp7m27G72joDU',
  NOW(),
  NOW()
)
ON DUPLICATE KEY UPDATE
  password = VALUES(password),
  updated_at = NOW();

-- Verify insertion
SELECT id, name, email, SUBSTR(password, 1, 20) as password_hash_prefix FROM users WHERE email = 'student@example.com';
