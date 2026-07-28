<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Every logged-in account needs this (e.g. to populate the department
// dropdown when submitting a ticket), not just IT — so requireLogin()
// only, not requireIT().
requireLogin();

$stmt = $pdo->query('SELECT id, name, is_active, created_at AS createdAt FROM departments ORDER BY name ASC');
$departments = $stmt->fetchAll();
foreach ($departments as &$d) {
    $d['is_active'] = (bool)$d['is_active'];
    $d['id'] = (int)$d['id'];
}
unset($d);

echo json_encode(['departments' => $departments]);