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

if ($id === '' || $text === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Kulang ang reply text.']));
}

$user = currentUser();

// Person-to-person lang: yung IT o yung TALAGANG gumawa ng ticket (owner) lang
// ang pwedeng magreply — hindi lahat ng miyembro ng department na 'yun.
$isIT = ($user['department'] ?? '') === 'IT Department';

$stmt = $pdo->prepare('SELECT created_by FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticketRow = $stmt->fetch();

if (!$ticketRow) {
    http_response_code(404);
    die(json_encode(['error' => 'Hindi nahanap ang ticket.']));
}

$isOwner = $ticketRow['created_by'] !== null && (int)$ticketRow['created_by'] === (int)$user['id'];

if (!$isIT && !$isOwner) {
    http_response_code(403);
    die(json_encode(['error' => 'Ikaw lang na gumawa ng ticket na ito (o IT) ang pwedeng magreply dito.']));
}

$stmt = $pdo->prepare('INSERT INTO ticket_comments (ticket_id, author, message) VALUES (?, ?, ?)');
$stmt->execute([$id, $user['fullname'], $text]);

$stmt = $pdo->prepare('UPDATE tickets SET updated_at = NOW() WHERE id = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('SELECT author, message AS text, created_at AS createdAt FROM ticket_comments WHERE ticket_id = ? ORDER BY created_at ASC');
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

echo json_encode(['comments' => $comments]);
