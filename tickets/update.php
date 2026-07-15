<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireIT();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$id = $input['id'] ?? '';

if ($id === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Kulang ang ticket id.']));
}

$department  = trim($input['department'] ?? '');
$category    = trim($input['category'] ?? '');
$priority    = trim($input['priority'] ?? '');
$description = trim($input['description'] ?? '');

if ($department === '' || $category === '' || $description === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Kulang ang mga required fields.']));
}
if (!in_array($priority, ['Low', 'Medium', 'High', 'Urgent'], true)) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid priority.']));
}

$stmt = $pdo->prepare('SELECT id FROM tickets WHERE id = ?');
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    http_response_code(404);
    die(json_encode(['error' => 'Hindi nahanap ang ticket.']));
}

$stmt = $pdo->prepare(
    'UPDATE tickets SET department = ?, category = ?, priority = ?, description = ?, updated_at = NOW() WHERE id = ?'
);
$stmt->execute([$department, $category, $priority, $description, $id]);

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();
$ticket['attachments'] = $ticket['attachments_json'] ? json_decode($ticket['attachments_json'], true) : [];
unset($ticket['attachments_json']);
$ticket['created_by'] = $ticket['created_by'] !== null ? (int)$ticket['created_by'] : null;

$stmt = $pdo->prepare('SELECT author, message AS text, created_at AS createdAt FROM ticket_comments WHERE ticket_id = ? ORDER BY created_at ASC');
$stmt->execute([$id]);
$ticket['comments'] = $stmt->fetchAll();

echo json_encode(['ticket' => $ticket]);
