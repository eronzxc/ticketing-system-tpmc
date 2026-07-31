<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/notify.php';

// Same permission as the single-ticket version: only IT can change status.
requireIT();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$ids = $input['ids'] ?? [];
$status = $input['status'] ?? '';

if (!is_array($ids) || count($ids) === 0) {
    http_response_code(400);
    die(json_encode(['error' => 'No tickets were selected.']));
}
if (!in_array($status, ['Open', 'In progress', 'Resolved'], true)) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid status.']));
}

$user = currentUser();
$updated = [];
$skipped = [];

// One ticket at a time (not a single big UPDATE ... WHERE id IN (...))
// so each ticket still gets its own notification and its own
// resolved_by/resolved_at handling, exactly like the single-ticket
// update_status.php — bulk is just this same logic run in a loop.
foreach ($ids as $id) {
    $id = (string)$id;

    $stmt = $pdo->prepare('SELECT status, created_by FROM tickets WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        $skipped[] = $id;
        continue;
    }

    if ($status === 'Resolved') {
        $stmt = $pdo->prepare(
            'UPDATE tickets SET status = ?, updated_at = NOW(), resolved_at = NOW(), resolved_by = ? WHERE id = ?'
        );
        $stmt->execute([$status, $user['fullname'], $id]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE tickets SET status = ?, updated_at = NOW(), resolved_at = NULL, resolved_by = NULL WHERE id = ?'
        );
        $stmt->execute([$status, $id]);
    }

    if ($existing['status'] !== $status && $existing['created_by'] !== null) {
        if ($status === 'In progress') {
            notifyUser($pdo, (int)$existing['created_by'], $id, 'in_progress', "Ticket $id is now in progress.");
        } elseif ($status === 'Resolved') {
            notifyUser($pdo, (int)$existing['created_by'], $id, 'resolved', "Ticket $id has been resolved.");
        }
    }

    $updated[] = $id;
}

echo json_encode(['updated' => $updated, 'skipped' => $skipped]);
