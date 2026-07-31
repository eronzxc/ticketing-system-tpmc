<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$ids = $input['ids'] ?? [];

if (!is_array($ids) || count($ids) === 0) {
    http_response_code(400);
    die(json_encode(['error' => 'No tickets were selected.']));
}

$user = currentUser();
$isIT = ($user['department'] ?? '') === 'IT Department';

// Same permission rule as the single-ticket delete.php: IT can delete
// any ticket; a non-IT requester can only delete their own, and only if
// their department is allowed to (can_delete_tickets, checked live from
// the DB, not the session).
$canDeleteOwn = false;
if (!$isIT) {
    $stmt = $pdo->prepare('SELECT can_delete_tickets FROM users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $perm = $stmt->fetch();
    $canDeleteOwn = $perm && $perm['can_delete_tickets'];
}

$deleted = [];
$skipped = [];

foreach ($ids as $id) {
    $id = (string)$id;

    $stmt = $pdo->prepare('SELECT id, created_by, deleted_at FROM tickets WHERE id = ?');
    $stmt->execute([$id]);
    $ticket = $stmt->fetch();

    if (!$ticket || $ticket['deleted_at'] !== null) {
        $skipped[] = $id;
        continue;
    }

    $isOwner = $ticket['created_by'] !== null && (int)$ticket['created_by'] === (int)$user['id'];

    if (!$isIT && !($isOwner && $canDeleteOwn)) {
        $skipped[] = $id;
        continue;
    }

    $stmt = $pdo->prepare('UPDATE tickets SET deleted_at = NOW(), deleted_by = ? WHERE id = ?');
    $stmt->execute([$user['fullname'], $id]);
    $deleted[] = $id;
}

echo json_encode(['deleted' => $deleted, 'skipped' => $skipped]);
