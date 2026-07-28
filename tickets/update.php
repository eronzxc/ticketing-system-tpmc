<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/notify.php';

requireIT();

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

// Only these fields can be edited here: Status, Due date, Date resolved,
// Resolved by, and Remarks. We don't touch the original request
// (department, category, priority, description) so the requester's
// submission is never overwritten. Status changes go through this
// endpoint too (behind a confirmation dialog) so it can't be changed by
// an accidental click.
$status     = trim($input['status'] ?? '');
$dueDate    = trim($input['due_date'] ?? '');
$resolvedAt = trim($input['resolved_at'] ?? '');
$resolvedByInput = trim($input['resolved_by'] ?? '');
$remarks    = trim($input['remarks'] ?? '');

if (!in_array($status, ['Open', 'In progress', 'Resolved'], true)) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid status.']));
}

$stmt = $pdo->prepare('SELECT status, resolved_at, resolved_by, created_by FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$existing = $stmt->fetch();
if (!$existing) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}

$user = currentUser();
$dueDateVal = $dueDate !== '' ? date('Y-m-d H:i:s', strtotime($dueDate)) : null;
$remarksVal = $remarks !== '' ? $remarks : null;

if ($status === 'Resolved') {
    // "Resolved by" is picked from a dropdown of IT staff (like an Excel
    // dropdown) instead of always auto-filling the logged-in account —
    // this lets one IT member log a resolution on a teammate's behalf.
    // Still validated against real, active IT accounts so it can't be
    // set to an arbitrary name.
    if ($resolvedByInput !== '') {
        $stmt = $pdo->prepare("SELECT fullname FROM users WHERE fullname = ? AND department = 'IT Department' AND is_active = 1");
        $stmt->execute([$resolvedByInput]);
        $validName = $stmt->fetchColumn();
        $resolvedByVal = $validName !== false ? $validName : $user['fullname'];
    } else {
        $resolvedByVal = $existing['resolved_by'] ?? $user['fullname'];
    }

    $resolvedAtVal = $resolvedAt !== ''
        ? date('Y-m-d H:i:s', strtotime($resolvedAt))
        : ($existing['status'] === 'Resolved' ? $existing['resolved_at'] : date('Y-m-d H:i:s'));
} else {
    $resolvedAtVal = null;
    $resolvedByVal = null;
}

$stmt = $pdo->prepare(
    'UPDATE tickets SET status = ?, due_date = ?, resolved_at = ?, resolved_by = ?, remarks = ?, updated_at = NOW() WHERE id = ?'
);
$stmt->execute([$status, $dueDateVal, $resolvedAtVal, $resolvedByVal, $remarksVal, $id]);

// Only notify on an actual status change, and only the requester who
// owns the ticket (not every member of their department).
if ($existing['status'] !== $status && $existing['created_by'] !== null) {
    if ($status === 'In progress') {
        notifyUser($pdo, (int)$existing['created_by'], $id, 'in_progress', "Ticket $id is now in progress.");
    } elseif ($status === 'Resolved') {
        notifyUser($pdo, (int)$existing['created_by'], $id, 'resolved', "Ticket $id has been resolved.");
    }
}

$stmt = $pdo->prepare(
    'SELECT t.*, u.fullname AS assigned_to_name
     FROM tickets t
     LEFT JOIN users u ON u.id = t.assigned_to
     WHERE t.id = ?'
);
$stmt->execute([$id]);
$ticket = $stmt->fetch();
$ticket['attachments'] = $ticket['attachments_json'] ? json_decode($ticket['attachments_json'], true) : [];
unset($ticket['attachments_json']);
$ticket['created_by'] = $ticket['created_by'] !== null ? (int)$ticket['created_by'] : null;
$ticket['assigned_to'] = $ticket['assigned_to'] !== null ? (int)$ticket['assigned_to'] : null;

$stmt = $pdo->prepare('SELECT id, author, author_id AS authorId, message AS text, created_at AS createdAt, edited_at AS editedAt FROM ticket_comments WHERE ticket_id = ? ORDER BY created_at ASC');
$stmt->execute([$id]);
$ticket['comments'] = $stmt->fetchAll();

echo json_encode(['ticket' => $ticket]);
