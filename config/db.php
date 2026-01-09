<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Default XAMPP user
define('DB_PASS', '');         // Default XAMPP password (empty)
define('DB_NAME', 'MauBakery');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // Create PDO instance
    $conn = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // If connection fails, show error
    die("Connection failed: " . $e->getMessage());
}
?>