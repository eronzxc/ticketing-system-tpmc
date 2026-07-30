<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$username = trim($input['username'] ?? '');
$code     = trim($input['code'] ?? '');
$password = $input['password'] ?? '';
$confirm  = $input['confirmPassword'] ?? '';

if ($username === '' || $code === '' || $password === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Please fill in all fields.']));
}
if (strlen($password) < 2) {
    http_response_code(400);
    die(json_encode(['error' => 'Password must be at least 2 characters.']));
}
if ($password !== $confirm) {
    http_response_code(400);
    die(json_encode(['error' => 'Password and confirm password do not match.']));
}

$stmt = $pdo->prepare('SELECT id, reset_code, reset_code_expires FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || $user['reset_code'] === null) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid or expired reset code. Please request a new one.']));
}
if ($user['reset_code'] !== $code) {
    http_response_code(400);
    die(json_encode(['error' => 'Incorrect reset code.']));
}
if (strtotime($user['reset_code_expires']) < time()) {
    http_response_code(400);
    die(json_encode(['error' => 'This reset code has expired. Please request a new one.']));
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// Clear the reset code so it can't be reused, and set the new password.
$stmt = $pdo->prepare('UPDATE users SET password_hash = ?, reset_code = NULL, reset_code_expires = NULL WHERE id = ?');
$stmt->execute([$hash, $user['id']]);

echo json_encode(['success' => true]);
