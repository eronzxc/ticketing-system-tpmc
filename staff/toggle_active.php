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
$targetId = isset($input['staff_id']) ? (int)$input['staff_id'] : 0;
$active = isset($input['active']) ? (bool)$input['active'] : null;

if ($targetId <= 0 || $active === null) {
    http_response_code(400);
    die(json_encode(['error' => 'staff_id and active are required.']));
}

$stmt = $pdo->prepare('SELECT id FROM it_staff WHERE id = ?');
$stmt->execute([$targetId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    die(json_encode(['error' => 'Staff name not found.']));
}

$stmt = $pdo->prepare('UPDATE it_staff SET is_active = ? WHERE id = ?');
$stmt->execute([$active ? 1 : 0, $targetId]);

echo json_encode(['ok' => true]);
