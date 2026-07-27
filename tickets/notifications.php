<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/notify.php';

requireLogin();

$user = currentUser();
$isIT = ($user['department'] ?? '') === 'IT Department';

$action = $_GET['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'mark_read' : 'list');

if ($action === 'list') {
    // Lazy due-soon check only needs to run for IT, since only IT gets
    // due_soon notifications.
    if ($isIT) {
        checkDueSoonTickets($pdo);
    }

    $stmt = $pdo->prepare(
        'SELECT id, ticket_id AS ticketId, type, message, is_read AS isRead, created_at AS createdAt
         FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50'
    );
    $stmt->execute([$user['id']]);
    $notifications = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$user['id']]);
    $unreadCount = (int)$stmt->fetchColumn();

    echo json_encode(['notifications' => $notifications, 'unreadCount' => $unreadCount]);
    exit;
}

if ($action === 'mark_read') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = $input['id'] ?? null;

    if ($id) {
        // Mark one, scoped to this user so you can't mark someone else's.
        $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user['id']]);
    } else {
        // Mark all as read.
        $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        $stmt->execute([$user['id']]);
    }

    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action.']);
