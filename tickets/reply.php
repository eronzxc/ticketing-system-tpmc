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
$text = trim($input['text'] ?? '');
$attachments = $input['attachments'] ?? [];

if ($id === '' || $text === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Reply text is required.']));
}

$user = currentUser();

// Person-to-person only: IT or whoever ACTUALLY created the ticket (the
// owner) can reply — not just any member of that department.
$isIT = ($user['department'] ?? '') === 'IT Department';

$stmt = $pdo->prepare('SELECT created_by FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticketRow = $stmt->fetch();

if (!$ticketRow) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}

$isOwner = $ticketRow['created_by'] !== null && (int)$ticketRow['created_by'] === (int)$user['id'];

if (!$isIT && !$isOwner) {
    http_response_code(403);
    die(json_encode(['error' => 'Only the ticket owner (or IT) can reply to this ticket.']));
}

$attachmentsJson = !empty($attachments) ? json_encode($attachments) : null;

$stmt = $pdo->prepare('INSERT INTO ticket_comments (ticket_id, author, author_id, message, attachments_json) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$id, $user['fullname'], $user['id'], $text, $attachmentsJson]);

$stmt = $pdo->prepare('UPDATE tickets SET updated_at = NOW() WHERE id = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('SELECT id, author, author_id AS authorId, message AS text, attachments_json, created_at AS createdAt, edited_at AS editedAt FROM ticket_comments WHERE ticket_id = ? ORDER BY created_at ASC');
$stmt->execute([$id]);
$comments = $stmt->fetchAll();
foreach ($comments as &$c) {
    $c['attachments'] = $c['attachments_json'] ? json_decode($c['attachments_json'], true) : [];
    unset($c['attachments_json']);
}
unset($c);

echo json_encode(['comments' => $comments]);
