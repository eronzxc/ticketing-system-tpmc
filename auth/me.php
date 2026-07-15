<?php
require_once __DIR__ . '/../config/session.php';

echo json_encode(['user' => currentUser()]);
