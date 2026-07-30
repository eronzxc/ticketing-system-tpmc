<?php
// config/db.php — single place for the database connection settings.
//
// The actual host/username/password now live in db_secrets.php (a
// separate, gitignored file), NOT here — this file used to have them
// hardcoded directly and committed to a public repo, which is unsafe.
// See config/db_secrets.example.php for setup instructions if that
// file is ever missing (e.g. fresh clone of the repo on another PC).

require_once __DIR__ . '/db_secrets.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed. Kausapin ang IT admin.']));
}
