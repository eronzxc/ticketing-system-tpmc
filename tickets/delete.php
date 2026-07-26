<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$id = $input['id'] ?? '';

if ($id === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Ticket ID is required.']));
}

$user = currentUser();
$isIT = ($user['department'] ?? '') === 'IT Department';

$stmt = $pdo->prepare('SELECT id, created_by, deleted_at FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}

if ($ticket['deleted_at'] !== null) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}

// IT can delete any ticket. A requester can only delete their own ticket
// (e.g. submitted by mistake) — never someone else's.
$isOwner = $ticket['created_by'] !== null && (int)$ticket['created_by'] === (int)$user['id'];

if (!$isIT && !$isOwner) {
    http_response_code(403);
    die(json_encode(['error' => 'Only IT or the ticket owner can delete this ticket.']));
}

// Soft delete only: the row (and its ticket number) is kept in the
// database so numbering never gets reused and nothing is unrecoverable.
// It's just excluded from list/get results from now on.
$stmt = $pdo->prepare('UPDATE tickets SET deleted_at = NOW(), deleted_by = ? WHERE id = ?');
$stmt->execute([$user['fullname'], $id]);

echo json_encode(['success' => true, 'id' => $id]);
