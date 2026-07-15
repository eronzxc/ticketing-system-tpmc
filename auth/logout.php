<?php
require_once __DIR__ . '/../config/session.php';

$_SESSION = [];
session_destroy();

echo json_encode(['ok' => true]);
