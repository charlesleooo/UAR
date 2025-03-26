<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'uar_db');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    error_log("Database connection successful");
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}
?>
