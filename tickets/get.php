<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

$id = $_GET['id'] ?? '';
if ($id === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Ticket ID is required.']));
}

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket || $ticket['deleted_at'] !== null) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}

$ticket['attachments'] = $ticket['attachments_json'] ? json_decode($ticket['attachments_json'], true) : [];
unset($ticket['attachments_json']);
$ticket['created_by'] = $ticket['created_by'] !== null ? (int)$ticket['created_by'] : null;

$stmt = $pdo->prepare('SELECT id, author, author_id AS authorId, message AS text, attachments_json, created_at AS createdAt, edited_at AS editedAt FROM ticket_comments WHERE ticket_id = ? ORDER BY created_at ASC');
$stmt->execute([$id]);
$comments = $stmt->fetchAll();
foreach ($comments as &$c) {
    $c['attachments'] = $c['attachments_json'] ? json_decode($c['attachments_json'], true) : [];
    unset($c['attachments_json']);
}
unset($c);
$ticket['comments'] = $comments;

echo json_encode(['ticket' => $ticket]);
