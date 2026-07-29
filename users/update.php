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
$targetId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
$fullname = trim($input['fullname'] ?? '');
$username = trim($input['username'] ?? '');
$department = trim($input['department'] ?? '');
$password = $input['password'] ?? '';

if ($targetId <= 0 || $fullname === '' || $username === '' || $department === '') {
    http_response_code(400);
    die(json_encode(['error' => 'user_id, fullname, username, and department are required.']));
}
if ($password !== '' && strlen($password) < 6) {
    http_response_code(400);
    die(json_encode(['error' => 'New password must be at least 6 characters.']));
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE id = ?');
$stmt->execute([$targetId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    die(json_encode(['error' => 'User not found.']));
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
$stmt->execute([$username, $targetId]);
if ($stmt->fetch()) {
    http_response_code(409);
    die(json_encode(['error' => 'This username is already taken.']));
}

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE users SET fullname = ?, username = ?, department = ?, password_hash = ? WHERE id = ?');
    $stmt->execute([$fullname, $username, $department, $hash, $targetId]);
} else {
    $stmt = $pdo->prepare('UPDATE users SET fullname = ?, username = ?, department = ? WHERE id = ?');
    $stmt->execute([$fullname, $username, $department, $targetId]);
}

$stmt = $pdo->prepare('SELECT id, fullname, username, department, is_active, created_at AS createdAt FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$user = $stmt->fetch();
$user['is_active'] = (bool)$user['is_active'];
$user['id'] = (int)$user['id'];

echo json_encode(['user' => $user]);
