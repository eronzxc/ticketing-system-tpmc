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
    die(json_encode(['error' => 'Kulang ang username o password.']));
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$row = $stmt->fetch();

if (!$row || !password_verify($password, $row['password_hash'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Mali ang username o password.']));
}

$user = [
    'id'         => $row['id'],
    'fullname'   => $row['fullname'],
    'username'   => $row['username'],
    'department' => $row['department'],
];

$_SESSION['user'] = $user;

echo json_encode(['user' => $user]);
