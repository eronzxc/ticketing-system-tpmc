<?php
/**
 * Notification helpers. require_once this alongside db.php in any
 * endpoint that needs to create or read notifications.
 */

function notifyUser(PDO $pdo, int $userId, string $ticketId, string $type, string $message): void {
    $stmt = $pdo->prepare(
        'INSERT INTO notifications (user_id, ticket_id, type, message) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $ticketId, $type, $message]);
}

// Notify every IT Department user at once (used for new_ticket, due_soon).
function notifyIT(PDO $pdo, string $ticketId, string $type, string $message): void {
    $stmt = $pdo->query("SELECT id FROM users WHERE department = 'IT Department' AND is_active = 1");
    $itUsers = $stmt->fetchAll();
    foreach ($itUsers as $u) {
        notifyUser($pdo, (int)$u['id'], $ticketId, $type, $message);
    }
}

/**
 * Lazy check for tickets nearing their due date, run opportunistically
 * (e.g. whenever IT loads the ticket list) instead of a cron job —
 * same pattern as the 30-day auto-purge in list_deleted.php.
 * Flags tickets due within 24 hours (and not yet resolved) that haven't
 * already been flagged, so it only fires once per ticket.
 */
function checkDueSoonTickets(PDO $pdo): void {
    $stmt = $pdo->query(
        "SELECT id, due_date FROM tickets
         WHERE deleted_at IS NULL
           AND status != 'Resolved'
           AND due_soon_notified = 0
           AND due_date IS NOT NULL
           AND due_date <= DATE_ADD(NOW(), INTERVAL 24 HOUR)"
    );
    $dueSoon = $stmt->fetchAll();

    foreach ($dueSoon as $t) {
        notifyIT($pdo, $t['id'], 'due_soon', "Ticket {$t['id']} is due soon.");
        $mark = $pdo->prepare('UPDATE tickets SET due_soon_notified = 1 WHERE id = ?');
        $mark->execute([$t['id']]);
    }
}
