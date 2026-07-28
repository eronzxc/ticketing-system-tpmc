<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireIT();

$stmt = $pdo->query('SELECT id, full_name AS fullName, is_active AS isActive, created_at AS createdAt FROM it_staff ORDER BY full_name ASC');
$staff = $stmt->fetchAll();
foreach ($staff as &$s) {
    $s['id'] = (int)$s['id'];
    $s['isActive'] = (bool)$s['isActive'];
}
unset($s);

echo json_encode(['staff' => $staff]);
