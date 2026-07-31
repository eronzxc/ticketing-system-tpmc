<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

$user = currentUser();
$isIT = ($user['department'] ?? '') === 'IT Department';

// IT sees every ticket. Every other department only sees tickets that
// belong to their own department — not just ones they personally
// created, so e.g. Accounting sees all "Accounting" tickets even if a
// different Accounting staff member (or IT, submitting on their behalf)
// created it.
if ($isIT) {
    $stmt = $pdo->query(
        'SELECT * FROM tickets
         WHERE deleted_at IS NULL
         ORDER BY created_at DESC'
    );
    $rows = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare(
        'SELECT * FROM tickets
         WHERE deleted_at IS NULL AND department = ?
         ORDER BY created_at DESC'
    );
    $stmt->execute([$user['department']]);
    $rows = $stmt->fetchAll();
}

$tickets = array_map(function ($row) {
    $row['attachments'] = $row['attachments_json'] ? json_decode($row['attachments_json'], true) : [];
    unset($row['attachments_json']);
    $row['created_by'] = $row['created_by'] !== null ? (int)$row['created_by'] : null;
    $row['assigned_to'] = $row['assigned_to'] !== null ? (int)$row['assigned_to'] : null;
    return $row;
}, $rows);

echo json_encode(['tickets' => $tickets]);
