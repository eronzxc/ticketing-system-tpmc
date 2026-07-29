<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Every logged-in account needs this to populate the department dropdown
// when submitting a ticket, not just IT — so requireLogin() only.
requireLogin();

// Departments are derived from active accounts, not a separate table:
// a department only shows up here if someone can actually log in as that
// department to submit tickets. Disabling an account automatically hides
// it here too; re-enabling brings it back.
$stmt = $pdo->query("SELECT DISTINCT department FROM users WHERE is_active = 1 ORDER BY department ASC");
$departments = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['departments' => $departments]);