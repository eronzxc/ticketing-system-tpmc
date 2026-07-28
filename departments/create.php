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
$name = trim($input['name'] ?? '');

if ($name === '') {
    http_response_code(400);
    die(json_encode(['error' => 'Department name is required.']));
}

$stmt = $pdo->prepare('SELECT id, is_active FROM departments WHERE name = ?');
$stmt->execute([$name]);
$existing = $stmt->fetch();

if ($existing) {
    if ($existing['is_active']) {
        http_response_code(409);
        die(json_encode(['error' => 'This department already exists.']));
    }
    // It exists but was deactivated — reactivate it instead of creating
    // a duplicate row.
    $stmt = $pdo->prepare('UPDATE departments SET is_active = 1 WHERE id = ?');
    $stmt->execute([$existing['id']]);
    $id = $existing['id'];
} else {
    $stmt = $pdo->prepare('INSERT INTO departments (name) VALUES (?)');
    $stmt->execute([$name]);
    $id = $pdo->lastInsertId();
}

$stmt = $pdo->prepare('SELECT id, name, is_active, created_at AS createdAt FROM departments WHERE id = ?');
$stmt->execute([$id]);
$department = $stmt->fetch();
$department['is_active'] = (bool)$department['is_active'];
$department['id'] = (int)$department['id'];

echo json_encode(['department' => $department]);