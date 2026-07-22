<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$user = currentUser();
$isIT = ($user['department'] ?? '') === 'IT Department';

// Restoring is IT-only, even though a requester is allowed to delete
// their own ticket. This keeps "undo" centralized with IT so a ticket
// number can't quietly reappear without their knowledge.
if (!$isIT) {
    http_response_code(403);
    die(json_encode(['error' => 'Only IT can restore deleted tickets.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$id = $input['id'] ?? '';

if ($id === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Ticket ID is required.']));
}

$stmt = $pdo->prepare('SELECT id, deleted_at FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}
if ($ticket['deleted_at'] === null) {
    http_response_code(400);
    die(json_encode(['error' => 'This ticket is not deleted.']));
}

$stmt = $pdo->prepare('UPDATE tickets SET deleted_at = NULL, deleted_by = NULL WHERE id = ?');
$stmt->execute([$id]);

echo json_encode(['success' => true, 'id' => $id]);
