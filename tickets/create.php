<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$requester   = trim($input['requester'] ?? '');
$department  = trim($input['department'] ?? '');
$category    = trim($input['category'] ?? '');
$priority    = trim($input['priority'] ?? '');
$description = trim($input['description'] ?? '');
$attachments = $input['attachments'] ?? [];

if ($requester === '' || $department === '' || $description === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Kulang ang mga required fields.']));
}
if (!in_array($priority, ['Low', 'Medium', 'High', 'Urgent'], true)) {
    $priority = 'Medium';
}

// Generate ticket ID: IT-{year}-{0001, 0002, ...}
$year = date('Y');
$stmt = $pdo->prepare("SELECT id FROM tickets WHERE id LIKE ? ORDER BY id DESC LIMIT 1");
$stmt->execute(["IT-$year-%"]);
$last = $stmt->fetchColumn();
$nextNum = $last ? ((int)substr($last, -4) + 1) : 1;
$ticketId = sprintf('IT-%s-%04d', $year, $nextNum);

$dueDaysMap = ['Urgent' => 1, 'High' => 2, 'Medium' => 3, 'Low' => 5];
$dueDays = $dueDaysMap[$priority] ?? 3;
$dueDate = date('Y-m-d H:i:s', strtotime("+$dueDays days"));

$attachmentsJson = !empty($attachments) ? json_encode($attachments) : null;

$stmt = $pdo->prepare(
    'INSERT INTO tickets (id, requester, department, category, priority, description, status, due_date, attachments_json)
     VALUES (?, ?, ?, ?, ?, ?, "Open", ?, ?)'
);
$stmt->execute([$ticketId, $requester, $department, $category, $priority, $description, $dueDate, $attachmentsJson]);

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();
$ticket['attachments'] = $attachments;

echo json_encode(['ticket' => $ticket]);
