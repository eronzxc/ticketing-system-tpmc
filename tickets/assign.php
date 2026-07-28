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
// assigned_to can be an IT user id, or null/0 to unassign.
$assignedTo = isset($input['assigned_to']) && $input['assigned_to'] !== null && $input['assigned_to'] !== ''
    ? (int)$input['assigned_to']
    : null;

if ($id === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Ticket ID is required.']));
}

$stmt = $pdo->prepare('SELECT id, assigned_to FROM tickets WHERE id = ? AND deleted_at IS NULL');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}

// If assigning (not unassigning), the target must be an active IT Department user.
if ($assignedTo !== null) {
    $stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE id = ? AND department = 'IT Department' AND is_active = 1");
    $stmt->execute([$assignedTo]);
    $targetUser = $stmt->fetch();
    if (!$targetUser) {
        http_response_code(400);
        die(json_encode(['error' => 'Can only assign to an active IT Department staff member.']));
    }
}

$stmt = $pdo->prepare('UPDATE tickets SET assigned_to = ?, updated_at = NOW() WHERE id = ?');
$stmt->execute([$assignedTo, $id]);

$actingUser = currentUser();

// Only notify if it's actually a new assignment to someone (not unassigning,
// and not re-notifying the exact same person who already had it).
if ($assignedTo !== null && $assignedTo !== (int)($ticket['assigned_to'] ?? 0)) {
    $message = ($assignedTo === (int)$actingUser['id'])
        ? "You claimed ticket $id."
        : "Ticket $id was assigned to you by {$actingUser['fullname']}.";
    notifyUser($pdo, $assignedTo, $id, 'assigned', $message);
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

echo json_encode(['ticket' => $ticket]);
