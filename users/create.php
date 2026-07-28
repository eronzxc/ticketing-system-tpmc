<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireIT();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$fullname   = trim($input['fullname'] ?? '');
$username   = trim($input['username'] ?? '');
$email      = trim($input['email'] ?? '');
$department = trim($input['department'] ?? '');
$password   = $input['password'] ?? '';

if ($fullname === '' || $username === '' || $email === '' || $department === '' || $password === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Please fill in all fields.']));
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['error' => 'Please enter a valid email address.']));
}
if (strlen($password) < 6) {
    http_response_code(400);
    die(json_encode(['error' => 'Password must be at least 6 characters.']));
}

// One account per department — the UNIQUE constraint on users.department
// enforces this at the DB level too, but we check here first for a
// friendlier error message.
$stmt = $pdo->prepare('SELECT id FROM users WHERE department = ?');
$stmt->execute([$department]);
if ($stmt->fetch()) {
    http_response_code(409);
    die(json_encode(['error' => 'A department account already exists for "' . $department . '". Edit that account instead of creating a new one.']));
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    http_response_code(409);
    die(json_encode(['error' => 'This username is already taken.']));
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    die(json_encode(['error' => 'This email is already registered.']));
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    'INSERT INTO users (fullname, username, email, password_hash, department, is_active) VALUES (?, ?, ?, ?, ?, 1)'
);
$stmt->execute([$fullname, $username, $email, $hash, $department]);

$stmt = $pdo->prepare('SELECT id, fullname, username, department, is_active, created_at AS createdAt FROM users WHERE id = ?');
$stmt->execute([$pdo->lastInsertId()]);
$user = $stmt->fetch();
$user['is_active'] = (bool)$user['is_active'];
$user['id'] = (int)$user['id'];

echo json_encode(['user' => $user]);
