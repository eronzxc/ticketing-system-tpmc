<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Username and password are required.']));
}

// ===== Rate limiting / lockout =====
// After 5 failed attempts on this username within 15 minutes, block
// further tries for 15 minutes. Only failed attempts are logged (see
// migration_12); a successful login clears this account's history.
// This is checked by username (not IP), since department accounts are
// shared and the concern is the account's password being guessed,
// regardless of which PC the attempt comes from.
const MAX_FAILED_ATTEMPTS = 5;
const LOCKOUT_MINUTES = 15;

// Opportunistic cleanup, same lazy pattern as due-soon notifications and
// the Recently Deleted auto-purge — no cron job needed. Keeps the table
// from growing forever.
$pdo->exec("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY)");

$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM login_attempts
     WHERE username = ? AND attempted_at >= DATE_SUB(NOW(), INTERVAL ' . LOCKOUT_MINUTES . ' MINUTE)'
);
$stmt->execute([$username]);
$recentFailures = (int)$stmt->fetchColumn();

if ($recentFailures >= MAX_FAILED_ATTEMPTS) {
    http_response_code(429);
    die(json_encode([
        'error' => "Too many failed attempts for this account. Please wait " . LOCKOUT_MINUTES . " minutes before trying again, or contact IT if this wasn't you."
    ]));
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$row = $stmt->fetch();

if (!$row || !password_verify($password, $row['password_hash'])) {
    // Log the failed attempt (whether the username doesn't exist at all,
    // or the password was wrong) so repeated guesses against either get
    // rate-limited the same way.
    $stmt = $pdo->prepare('INSERT INTO login_attempts (username) VALUES (?)');
    $stmt->execute([$username]);

    http_response_code(401);
    die(json_encode(['error' => 'Incorrect username or password.']));
}

if (isset($row['is_active']) && !$row['is_active']) {
    http_response_code(403);
    die(json_encode(['error' => 'This account has been disabled. Contact IT if you believe this is a mistake.']));
}

// Successful login — clear this username's failed-attempt history so a
// correct password isn't penalized by earlier mistakes.
$stmt = $pdo->prepare('DELETE FROM login_attempts WHERE username = ?');
$stmt->execute([$username]);

$user = [
    'id'         => $row['id'],
    'fullname'   => $row['fullname'],
    'username'   => $row['username'],
    'email'      => $row['email'] ?? null,
    'department' => $row['department'],
];

$_SESSION['user'] = $user;

echo json_encode(['user' => $user]);
