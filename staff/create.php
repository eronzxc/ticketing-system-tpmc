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
$fullName = trim($input['full_name'] ?? '');

if ($fullName === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Please enter a name.']));
}

$stmt = $pdo->prepare('SELECT id FROM it_staff WHERE full_name = ?');
$stmt->execute([$fullName]);
if ($stmt->fetch()) {
    http_response_code(400);
    die(json_encode(['error' => 'That name is already on the list.']));
}

$stmt = $pdo->prepare('INSERT INTO it_staff (full_name) VALUES (?)');
$stmt->execute([$fullName]);
$newId = (int)$pdo->lastInsertId();

echo json_encode(['ok' => true, 'staff' => ['id' => $newId, 'fullName' => $fullName, 'isActive' => true]]);
