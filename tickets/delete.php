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

if ($targetId <= 0) {
    http_response_code(400);
    die(json_encode(['error' => 'user_id is required.']));
}

$currentUser = currentUser();

if ($targetId === (int)$currentUser['id']) {
    http_response_code(400);
    die(json_encode(['error' => 'You cannot delete your own account.']));
}

$stmt = $pdo->prepare('SELECT id, department FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$target = $stmt->fetch();

if (!$target) {
    http_response_code(404);
    die(json_encode(['error' => 'User not found.']));
}

if ($target['department'] === 'IT Department') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE department = 'IT Department' AND id != ?");
    $stmt->execute([$targetId]);
    $otherIT = (int)$stmt->fetchColumn();
    if ($otherIT === 0) {
        http_response_code(400);
        die(json_encode(['error' => 'Cannot delete the last IT Department account.']));
    }
}

try {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$targetId]);
} catch (PDOException $e) {
    // Most likely a foreign key restriction (e.g. tickets they created still
    // reference them). Deleting would break those records, so guide the
    // admin toward Disable instead rather than crashing.
    http_response_code(409);
    die(json_encode(['error' => 'This account cannot be deleted because it still has tickets or replies linked to it. Disable the account instead to preserve that history.']));
}

echo json_encode(['ok' => true]);
