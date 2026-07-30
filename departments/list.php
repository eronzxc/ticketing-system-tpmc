<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Every logged-in account needs this to populate the department dropdown
// when submitting a ticket, not just IT — so requireLogin() only.
requireLogin();

// Departments now live in their own table (see migration_10), managed by
// IT independently of login accounts — so a department can exist (and be
// selectable) even before any account has been created for it yet.
$stmt = $pdo->query("SELECT name FROM departments WHERE is_active = 1 ORDER BY name ASC");
$departments = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['departments' => $departments]);