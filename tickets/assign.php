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

// assigned_it_staff is a name from the it_staff list (or null/blank to
// mean "IT Department, no specific technician yet"). Not tied to a login
// account anymore — the IT Department account is shared, so there's no
// single "assignee user" to point to. See migration_11 for context.
$assignedItStaff = trim($input['assigned_it_staff'] ?? '');
$assignedItStaff = $assignedItStaff !== '' ? $assignedItStaff : null;

if ($id === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Ticket ID is required.']));
}

$stmt = $pdo->prepare('SELECT id, assigned_it_staff FROM tickets WHERE id = ? AND deleted_at IS NULL');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    http_response_code(404);
    die(json_encode(['error' => 'Ticket not found.']));
}

$previousAssignee = $ticket['assigned_it_staff'];

$stmt = $pdo->prepare('UPDATE tickets SET assigned_it_staff = ?, updated_at = NOW() WHERE id = ?');
$stmt->execute([$assignedItStaff, $id]);

// Notify the whole IT Department account (not a specific person — nobody
// has their own login to notify) whenever a name is newly set or changed,
// so whoever is using the shared account next sees it as a reminder.
if ($assignedItStaff !== null && $assignedItStaff !== $previousAssignee) {
    $actingUser = currentUser();
    $message = "Ticket $id was assigned to $assignedItStaff by {$actingUser['fullname']}.";
    notifyIT($pdo, $id, 'assigned', $message);
}

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();
$ticket['attachments'] = $ticket['attachments_json'] ? json_decode($ticket['attachments_json'], true) : [];
unset($ticket['attachments_json']);
$ticket['created_by'] = $ticket['created_by'] !== null ? (int)$ticket['created_by'] : null;
$ticket['assigned_to'] = $ticket['assigned_to'] !== null ? (int)$ticket['assigned_to'] : null;

echo json_encode(['ticket' => $ticket]);
