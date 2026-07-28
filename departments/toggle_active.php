<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireIT();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed.']));
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$targetId = isset($input['department_id']) ? (int)$input['department_id'] : 0;
$active = isset($input['active']) ? (bool)$input['active'] : null;

if ($targetId <= 0 || $active === null) {
    http_response_code(400);
    die(json_encode(['error' => 'department_id and active are required.']));
}

$stmt = $pdo->prepare('SELECT id, name FROM departments WHERE id = ?');
$stmt->execute([$targetId]);
$target = $stmt->fetch();

if (!$target) {
    http_response_code(404);
    die(json_encode(['error' => 'Department not found.']));
}

// Don't allow deactivating "IT Department" itself — that would remove IT
// from its own dropdowns and lock the app's own admin department out.
if (!$active && $target['name'] === 'IT Department') {
    http_response_code(400);
    die(json_encode(['error' => 'IT Department cannot be deactivated.']));
}

$stmt = $pdo->prepare('UPDATE departments SET is_active = ? WHERE id = ?');
$stmt->execute([$active ? 1 : 0, $targetId]);

echo json_encode(['ok' => true]);