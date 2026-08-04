<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/department_helper.php';

requireIT();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$fullname   = trim($input['fullname'] ?? '');
$username   = trim($input['username'] ?? '');
$email      = trim($input['email'] ?? '');
$department = canonicalizeDepartment($pdo, trim($input['department'] ?? ''));
$password   = $input['password'] ?? '';

if ($fullname === '' || $username === '' || $department === '' || $password === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Please fill in all fields.']));
}
// Email is optional — department accounts are shared and don't have a
// real inbox, so only bother validating it if one was actually provided.
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['error' => 'Please enter a valid email address.']));
}
if (strlen($password) < 2) {
    http_response_code(400);
    die(json_encode(['error' => 'Password must be at least 2 characters.']));
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

if ($email !== '') {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        die(json_encode(['error' => 'This email is already registered.']));
    }
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// New department accounts default to NOT allowed to delete tickets
// (can_delete_tickets = 0) — IT has to explicitly opt a department in via
// "Manage users" > the permission toggle. Also enforced at the column's
// own DEFAULT (see migration_13) so this stays true even if some other
// code path ever inserts a user without setting this column.
$stmt = $pdo->prepare(
    'INSERT INTO users (fullname, username, email, password_hash, department, is_active, can_delete_tickets) VALUES (?, ?, ?, ?, ?, 1, 0)'
);
$stmt->execute([$fullname, $username, $email !== '' ? $email : null, $hash, $department]);

$stmt = $pdo->prepare('SELECT id, fullname, username, department, is_active, created_at AS createdAt FROM users WHERE id = ?');
$stmt->execute([$pdo->lastInsertId()]);
$user = $stmt->fetch();
$user['is_active'] = (bool)$user['is_active'];
$user['id'] = (int)$user['id'];

echo json_encode(['user' => $user]);
