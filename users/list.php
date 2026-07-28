<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireIT();

$stmt = $pdo->query('SELECT id, fullname, username, department, is_active, can_delete_tickets, created_at AS createdAt FROM users ORDER BY fullname ASC');
$users = $stmt->fetchAll();
foreach ($users as &$u) {
    $u['is_active'] = (bool)$u['is_active'];
    $u['can_delete_tickets'] = (bool)$u['can_delete_tickets'];
    $u['id'] = (int)$u['id'];
}
unset($u);

echo json_encode(['users' => $users]);
