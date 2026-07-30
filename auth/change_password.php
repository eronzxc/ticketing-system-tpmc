<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$currentPassword = $input['current_password'] ?? '';
$newPassword = $input['new_password'] ?? '';
$confirmNewPassword = $input['confirm_new_password'] ?? '';

if ($currentPassword === '' || $newPassword === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Current password and new password are required.']));
}

if (strlen($newPassword) < 2) {
    http_response_code(400);
    die(json_encode(['error' => 'New password must be at least 2 characters.']));
}

if ($newPassword !== $confirmNewPassword) {
    http_response_code(400);
    die(json_encode(['error' => 'New passwords do not match.']));
}

$currentUser = currentUser();

$stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
$stmt->execute([$currentUser['id']]);
$row = $stmt->fetch();

if (!$row || !password_verify($currentPassword, $row['password_hash'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Current password is incorrect.']));
}

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
$stmt->execute([$newHash, $currentUser['id']]);

echo json_encode(['ok' => true]);