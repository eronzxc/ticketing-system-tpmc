<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

$user = currentUser();
$isIT = ($user['department'] ?? '') === 'IT Department';

if (!$isIT) {
    http_response_code(403);
    die(json_encode(['error' => 'IT Department access only.']));
}

$stmt = $pdo->query('SELECT * FROM tickets WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC');
$rows = $stmt->fetchAll();

$tickets = array_map(function ($row) {
    $row['attachments'] = $row['attachments_json'] ? json_decode($row['attachments_json'], true) : [];
    unset($row['attachments_json']);
    $row['created_by'] = $row['created_by'] !== null ? (int)$row['created_by'] : null;
    return $row;
}, $rows);

echo json_encode(['tickets' => $tickets]);
