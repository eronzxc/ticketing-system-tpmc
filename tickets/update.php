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
$department = trim($input['department'] ?? '');

if ($targetId <= 0 || $fullname === '' || $department === '') {
    http_response_code(400);
    die(json_encode(['error' => 'user_id, fullname, and department are required.']));
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE id = ?');
$stmt->execute([$targetId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    die(json_encode(['error' => 'User not found.']));
}

$stmt = $pdo->prepare('UPDATE users SET fullname = ?, department = ? WHERE id = ?');
$stmt->execute([$fullname, $department, $targetId]);

$stmt = $pdo->prepare('SELECT id, fullname, username, department, is_active, created_at AS createdAt FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$user = $stmt->fetch();
$user['is_active'] = (bool)$user['is_active'];
$user['id'] = (int)$user['id'];

echo json_encode(['user' => $user]);
