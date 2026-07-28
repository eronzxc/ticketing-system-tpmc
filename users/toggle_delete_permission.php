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
$canDelete = isset($input['can_delete_tickets']) ? (bool)$input['can_delete_tickets'] : null;

if ($targetId <= 0 || $canDelete === null) {
    http_response_code(400);
    die(json_encode(['error' => 'user_id and can_delete_tickets are required.']));
}

$stmt = $pdo->prepare('SELECT id, department FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$target = $stmt->fetch();

if (!$target) {
    http_response_code(404);
    die(json_encode(['error' => 'User not found.']));
}

// IT Department can always delete tickets — this setting only applies to
// non-IT department accounts, so block accidental changes to IT itself.
if ($target['department'] === 'IT Department') {
    http_response_code(400);
    die(json_encode(['error' => 'IT Department can always delete tickets; this cannot be changed.']));
}

$stmt = $pdo->prepare('UPDATE users SET can_delete_tickets = ? WHERE id = ?');
$stmt->execute([$canDelete ? 1 : 0, $targetId]);

echo json_encode(['ok' => true]);