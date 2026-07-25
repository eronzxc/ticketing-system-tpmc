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
$active = isset($input['active']) ? (bool)$input['active'] : null;

if ($targetId <= 0 || $active === null) {
    http_response_code(400);
    die(json_encode(['error' => 'user_id and active are required.']));
}

$currentUser = currentUser();

if ($targetId === (int)$currentUser['id']) {
    http_response_code(400);
    die(json_encode(['error' => 'You cannot disable your own account.']));
}

$stmt = $pdo->prepare('SELECT id, department, is_active FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$target = $stmt->fetch();

if (!$target) {
    http_response_code(404);
    die(json_encode(['error' => 'User not found.']));
}

// Safety net: don't allow disabling the last remaining active IT account,
// which would lock everyone out of user/ticket management.
if (!$active && $target['department'] === 'IT Department') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE department = 'IT Department' AND is_active = 1 AND id != ?");
    $stmt->execute([$targetId]);
    $otherActiveIT = (int)$stmt->fetchColumn();
    if ($otherActiveIT === 0) {
        http_response_code(400);
        die(json_encode(['error' => 'Cannot disable the last active IT Department account.']));
    }
}

$stmt = $pdo->prepare('UPDATE users SET is_active = ? WHERE id = ?');
$stmt->execute([$active ? 1 : 0, $targetId]);

echo json_encode(['ok' => true]);
