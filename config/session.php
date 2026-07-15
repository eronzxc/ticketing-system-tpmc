<?php
// config/session.php — i-include ito sa simula ng bawat PHP file na kailangan malaman kung sino ang naka-login.
session_start();
header('Content-Type: application/json');

// Para hindi ma-cache ng browser ang session-dependent responses
header('Cache-Control: no-store, no-cache, must-revalidate');

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function requireLogin() {
    if (!currentUser()) {
        http_response_code(401);
        die(json_encode(['error' => 'Hindi naka-login.']));
    }
}

function requireIT() {
    requireLogin();
    if (($_SESSION['user']['department'] ?? '') !== 'IT Department') {
        http_response_code(403);
        die(json_encode(['error' => 'IT Department lang ang may access dito.']));
    }
}
